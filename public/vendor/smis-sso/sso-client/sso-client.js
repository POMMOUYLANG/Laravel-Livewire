var __require = /* @__PURE__ */ ((x) =>
    typeof require !== "undefined"
        ? require
        : typeof Proxy !== "undefined"
          ? new Proxy(x, {
                get: (a, b) =>
                    (typeof require !== "undefined" ? require : a)[b],
            })
          : x)(function (x) {
    if (typeof require !== "undefined") return require.apply(this, arguments);
    throw Error('Dynamic require of "' + x + '" is not supported');
});

// src/http.ts
var buildAuthUrl = (config) => new URL(config.probePath, config.authBaseUrl);
var fetchAuthorizations = async (config, session) => {
    const authUrl = new URL("/api/sso/authorizations", config.authBaseUrl);
    const fetchImpl = config.fetch ?? fetch;
    const response = await fetchImpl(authUrl.toString(), {
        headers: {
            Authorization: `Bearer ${session.accessToken}`,
            "X-SMIS-APP-KEY": config.appKey,
        },
    });
    if (!response.ok) {
        throw new Error(`Failed to load authorizations (${response.status})`);
    }
    return await response.json();
};
var fetchContextAuthorizations = async (config, session) => {
    const url = new URL("/api/sso/authorizations/context", config.authBaseUrl);
    const fetchImpl = config.fetch ?? fetch;
    const response = await fetchImpl(url.toString(), {
        headers: {
            Authorization: `Bearer ${session.accessToken}`,
        },
    });
    if (!response.ok) {
        throw new Error(
            `Failed to load contextual authorizations (${response.status})`,
        );
    }
    return await response.json();
};
var logoutSession = async (config, session) => {
    if (!session?.refreshToken) return;
    const url = new URL("/auth/logout", config.authBaseUrl);
    const fetchImpl = config.fetch ?? fetch;
    try {
        await fetchImpl(url.toString(), {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ refreshToken: session.refreshToken }),
        });
    } catch (error) {}
};

// src/storage.ts
var MemoryStorage = class {
    constructor() {
        this.store = /* @__PURE__ */ new Map();
    }
    getItem(key) {
        return this.store.has(key) ? this.store.get(key) : null;
    }
    setItem(key, value) {
        this.store.set(key, value);
    }
    removeItem(key) {
        this.store.delete(key);
    }
};
var getDefaultStorage = (preferred) => {
    if (typeof window !== "undefined") {
        if (preferred === "sessionStorage" && window.sessionStorage)
            return window.sessionStorage;
        if (preferred === "localStorage" && window.localStorage)
            return window.localStorage;
        if (!preferred || preferred === "memory") {
            if (window.localStorage) return window.localStorage;
            if (window.sessionStorage) return window.sessionStorage;
        }
    }
    return new MemoryStorage();
};
var storeSession = (storage, storageKey, session) => {
    if (!session) {
        storage.removeItem(storageKey);
        return;
    }
    storage.setItem(storageKey, JSON.stringify(session));
};
var readSession = (storage, storageKey) => {
    const value = storage.getItem(storageKey);
    if (!value) return null;
    try {
        return JSON.parse(value);
    } catch (error) {
        storage.removeItem(storageKey);
        return null;
    }
};

// src/jwt.ts
var textEncoder = new TextEncoder();
var textDecoder = new TextDecoder();
var base64Url = (data) => {
    const bytes = data instanceof ArrayBuffer ? new Uint8Array(data) : data;
    let str = "";
    for (let i = 0; i < bytes.length; i += 1) {
        str += String.fromCharCode(bytes[i]);
    }
    return btoa(str).replace(/=/g, "").replace(/\+/g, "-").replace(/\//g, "_");
};
var base64UrlDecode = (input) => {
    const padded = input.replace(/-/g, "+").replace(/_/g, "/");
    const base64 = padded + "=".repeat((4 - (padded.length % 4)) % 4);
    const binary =
        typeof atob === "function"
            ? atob(base64)
            : Buffer.from(base64, "base64").toString("binary");
    const bytes = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i += 1) {
        bytes[i] = binary.charCodeAt(i);
    }
    return bytes;
};
var base64UrlEncodeJson = (obj) => {
    const json = JSON.stringify(obj);
    return base64Url(textEncoder.encode(json));
};
var getNodeCrypto = () => {
    const req = typeof __require === "function" ? __require : void 0;
    if (!req) return null;
    try {
        return req("crypto");
    } catch {
        return null;
    }
};
var signHmacSha256 = async (secret, data) => {
    if (typeof window !== "undefined" && window.crypto?.subtle) {
        const key = await window.crypto.subtle.importKey(
            "raw",
            textEncoder.encode(secret),
            { name: "HMAC", hash: "SHA-256" },
            false,
            ["sign"],
        );
        const signature = await window.crypto.subtle.sign(
            "HMAC",
            key,
            textEncoder.encode(data),
        );
        return base64Url(signature);
    }
    const cryptoMod = getNodeCrypto();
    if (!cryptoMod?.createHmac) {
        throw new Error("crypto.createHmac is not available");
    }
    const hmac = cryptoMod.createHmac("sha256", secret);
    hmac.update(data);
    return hmac.digest("base64url");
};
var createHs256Jwt = async (secret, payload, options = {}) => {
    const header = { alg: "HS256", typ: "JWT", ...(options.header ?? {}) };
    const encodedHeader = base64UrlEncodeJson(header);
    const encodedPayload = base64UrlEncodeJson(payload);
    const unsigned = `${encodedHeader}.${encodedPayload}`;
    const signature = await signHmacSha256(secret, unsigned);
    return `${unsigned}.${signature}`;
};
var verifyHs256Jwt = async (token, secret) => {
    const parts = token.split(".");
    if (parts.length !== 3) {
        throw new Error("Invalid JWT format");
    }
    const [headerB64, payloadB64, signature] = parts;
    const unsigned = `${headerB64}.${payloadB64}`;
    const expected = await signHmacSha256(secret, unsigned);
    if (signature !== expected) {
        throw new Error("Invalid JWT signature");
    }
    const payloadBytes = base64UrlDecode(payloadB64);
    return JSON.parse(textDecoder.decode(payloadBytes));
};
var createAppProbeToken = async (appKey, expiresInSeconds = 300) => {
    const now = Math.floor(Date.now() / 1e3);
    return createHs256Jwt(appKey, {
        appKey,
        iat: now,
        exp: now + expiresInSeconds,
    });
};
var decodeJwtPayload = (token) => {
    const [, payloadB64] = token.split(".");
    if (!payloadB64) {
        throw new Error("Invalid JWT format");
    }
    const padded = payloadB64 + "=".repeat((4 - (payloadB64.length % 4)) % 4);
    const json =
        typeof atob === "function"
            ? atob(padded.replace(/-/g, "+").replace(/_/g, "/"))
            : Buffer.from(
                  padded.replace(/-/g, "+").replace(/_/g, "/"),
                  "base64",
              ).toString("utf8");
    return JSON.parse(json);
};

// src/env.ts
var hasProcessEnv = typeof process !== "undefined" && !!process.env;
var dotenvConfigured = false;
var configureDotenv = () => {
    if (!hasProcessEnv || dotenvConfigured) return;
    dotenvConfigured = true;
    const req = typeof __require === "function" ? __require : void 0;
    if (!req) return;
    try {
        const dotenv = req("dotenv");
        if (dotenv && typeof dotenv.config === "function") {
            dotenv.config();
        }
    } catch {}
};
configureDotenv();
var readEnv = (key) => {
    if (!hasProcessEnv) return void 0;
    const value = process.env?.[key];
    return typeof value === "string" && value.length > 0 ? value : void 0;
};
var readEnvString = (...keys) => {
    for (const key of keys) {
        const value = readEnv(key);
        if (value !== void 0) return value;
    }
    return void 0;
};
var readEnvNumber = (...keys) => {
    const raw = readEnvString(...keys);
    if (!raw) return void 0;
    const value = Number(raw);
    return Number.isFinite(value) ? value : void 0;
};

// src/config.ts
var inferAppKey = (config) => {
    const runtimeEnv =
        typeof globalThis !== "undefined" && globalThis.__SMIS_ENV__
            ? globalThis.__SMIS_ENV__
            : void 0;
    const globalAppKey =
        typeof globalThis !== "undefined"
            ? (globalThis.__SMIS_APP_KEY__ ??
              globalThis.SMIS_APP_KEY ??
              globalThis.APP_KEY)
            : void 0;
    return (
        config?.appKey ??
        readEnvString(
            "SMIS_APP_KEY",
            "NEXT_PUBLIC_SMIS_APP_KEY",
            "NEXTAUTH_SMIS_APP_KEY",
            "APP_KEY",
            "NEXT_PUBLIC_APP_KEY",
        ) ??
        runtimeEnv?.NEXT_PUBLIC_SMIS_APP_KEY ??
        globalAppKey
    );
};
var inferAuthBaseUrl = (config) =>
    config?.authBaseUrl ??
    readEnvString(
        "SMIS_AUTH_BASE_URL",
        "NEXT_PUBLIC_SMIS_AUTH_BASE_URL",
        "AUTH_BASE_URL",
        "NEXT_PUBLIC_AUTH_BASE_URL",
        "BASE_URL",
        "NEXT_PUBLIC_BASE_URL",
    ) ??
    (typeof globalThis !== "undefined"
        ? globalThis.__SMIS_ENV__?.NEXT_PUBLIC_SMIS_AUTH_BASE_URL
        : void 0);
var inferProbePath = (config) =>
    config?.probePath ??
    readEnvString("SMIS_PROBE_PATH", "NEXT_PUBLIC_SMIS_PROBE_PATH") ??
    (typeof globalThis !== "undefined"
        ? globalThis.__SMIS_ENV__?.NEXT_PUBLIC_SMIS_PROBE_PATH
        : void 0);
var inferStorage = (config) => {
    const env = readEnvString("SMIS_STORAGE", "NEXT_PUBLIC_SMIS_STORAGE");
    const preferred = config?.storage ?? env;
    if (preferred === "sessionStorage" || preferred === "memory")
        return preferred;
    return "localStorage";
};
var inferStorageKey = (config, appKey) =>
    config?.storageKey ??
    readEnvString("SMIS_STORAGE_KEY", "NEXT_PUBLIC_SMIS_STORAGE_KEY") ??
    `smis-sso:${appKey}`;
var inferTimeout = (config) =>
    config?.timeoutMs ??
    readEnvNumber("SMIS_TIMEOUT_MS", "NEXT_PUBLIC_SMIS_TIMEOUT_MS");
var inferPollInterval = (config) =>
    config?.pollIntervalMs ??
    readEnvNumber("SMIS_POLL_INTERVAL_MS", "NEXT_PUBLIC_SMIS_POLL_INTERVAL_MS");
var resolveConfig = (config) => {
    const appKey = inferAppKey(config);
    if (!appKey) {
        throw new Error(
            "SMIS SSO: appKey is required. Provide config.appKey or set SMIS_APP_KEY / NEXT_PUBLIC_SMIS_APP_KEY / NEXTAUTH_SMIS_APP_KEY.",
        );
    }
    const authBaseUrl =
        inferAuthBaseUrl(config) ?? "https://accounts.itc.edu.kh";
    const probePath = inferProbePath(config) ?? "/sso/probe";
    const storage = inferStorage(config) ?? "localStorage";
    const timeoutMs = inferTimeout(config) ?? 60 * 60 * 1e3;
    const pollIntervalMs = inferPollInterval(config) ?? 60 * 1e3;
    const storageKey = inferStorageKey(config, appKey);
    return {
        ...(config ?? {}),
        appKey,
        authBaseUrl,
        probePath,
        storage,
        storageKey,
        timeoutMs,
        pollIntervalMs,
    };
};

// src/client.ts
var AuthClient = class {
    constructor(config = {}) {
        this.config = config;
        this.resolvedConfig = resolveConfig(config);
        this.storage = getDefaultStorage(this.resolvedConfig.storage);
        this.storageKey = this.resolvedConfig.storageKey;
        this.timeoutMs = this.resolvedConfig.timeoutMs;
        this.pollIntervalMs = this.resolvedConfig.pollIntervalMs;
        this.authOrigin = new URL(this.resolvedConfig.authBaseUrl).origin;
    }
    getCachedSession() {
        const session = readSession(this.storage, this.storageKey);
        if (!session) return null;
        if (new Date(session.expiresAt).getTime() <= Date.now()) {
            storeSession(this.storage, this.storageKey, null);
            return null;
        }
        return session;
    }
    async ensureSession() {
        const cached = this.getCachedSession();
        if (cached) return cached;
        if (typeof window === "undefined") {
            throw new Error(
                "ensureSession requires a browser runtime to open the auth probe",
            );
        }
        const session = await this.launchAuthProbe();
        storeSession(this.storage, this.storageKey, session);
        return session;
    }
    async loadAuthorizations(session) {
        const resolvedSession = session ?? (await this.ensureSession());
        return fetchAuthorizations(this.resolvedConfig, resolvedSession);
    }
    async loadContextAuthorizations(session) {
        const resolvedSession = session ?? (await this.ensureSession());
        return fetchContextAuthorizations(this.resolvedConfig, resolvedSession);
    }
    /**
     * Returns user/token info and (optionally) contextual details such as employeeId/branches.
     * Set { fetchContext: true } to include contextual authorizations.
     */
    async user(options) {
        const fetchContext = options?.fetchContext ?? false;
        const session = options?.session ?? (await this.ensureSession());
        const info = this.decodeAccessToken(session.accessToken);
        if (!fetchContext) return info;
        const context = await this.loadContextAuthorizations(session);
        return {
            ...info,
            employeeId: context.employeeId,
            branches: context.branches,
        };
    }
    /**
     * Clears the locally cached session only (no network calls).
     */
    clearSession() {
        storeSession(this.storage, this.storageKey, null);
    }
    /**
     * Signs in, forcing a fresh probe if `force` is true even when a cached session exists.
     */
    async signIn(options) {
        const force = options?.force ?? false;
        if (force) {
            this.clearSession();
        }
        return this.ensureSession();
    }
    /**
     * Signs out: calls the auth portal logout (best-effort) and clears all local state.
     */
    async signOut(session) {
        const current = session ?? this.getCachedSession() ?? void 0;
        await logoutSession(this.resolvedConfig, current).catch(() => void 0);
        this.clearSession();
    }
    /**
     * Switches user by clearing the current session and forcing a new sign-in.
     */
    async switchUser() {
        await this.signOut();
        return this.signIn({ force: true });
    }
    launchAuthProbe() {
        return new Promise((resolve, reject) => {
            const authUrl = buildAuthUrl(this.resolvedConfig);
            authUrl.searchParams.set("appKey", this.resolvedConfig.appKey);
            createAppProbeToken(this.resolvedConfig.appKey)
                .then((token) => {
                    authUrl.searchParams.set("token", token);
                })
                .catch((error) => {
                    console.warn(
                        "SMIS SSO: app token signing unavailable, falling back to appKey probe.",
                        error,
                    );
                })
                .finally(() => {
                    openPopup(authUrl.toString());
                });
            const openPopup = (url) => {
                const popup = window.open(
                    url,
                    "_blank",
                    "width=580,height=640",
                );
                if (!popup) {
                    reject(new Error("Unable to open auth probe window"));
                    return;
                }
                const timeoutId = window.setTimeout(() => {
                    window.removeEventListener("message", messageHandler);
                    popup.close();
                    reject(new Error("Auth probe timed out"));
                }, this.timeoutMs);
                const messageHandler = (event) => {
                    if (event.origin !== this.authOrigin) return;
                    if (!event.data || event.data.type !== "smis:sso:session")
                        return;
                    window.clearTimeout(timeoutId);
                    window.removeEventListener("message", messageHandler);
                    popup.close();
                    resolve(event.data.payload);
                };
                window.addEventListener("message", messageHandler);
                const intervalId = window.setInterval(() => {
                    if (popup.closed) {
                        window.clearInterval(intervalId);
                        window.clearTimeout(timeoutId);
                        window.removeEventListener("message", messageHandler);
                        reject(
                            new Error(
                                "Auth probe was closed before completing sign-in",
                            ),
                        );
                    }
                }, this.pollIntervalMs);
            };
        });
    }
    decodeAccessToken(token) {
        const payload = decodeJwtPayload(token);
        return {
            userId: String(payload.sub ?? ""),
            username: String(payload.username ?? ""),
            // appKey: String(payload.appKey ?? this.resolvedConfig.appKey),
            roles: Array.isArray(payload.roles) ? payload.roles : [],
            permissions: Array.isArray(payload.permissions)
                ? payload.permissions
                : [],
        };
    }
};
var createAuthProbeResponse = (session) => {
    if (typeof window === "undefined") return;
    const message = {
        type: "smis:sso:session",
        payload: session,
    };
    window.opener?.postMessage(message, window.location.origin);
};

// src/client-factory.ts
var caches = /* @__PURE__ */ new Map();
var getCache = (scope) => {
    const existing = caches.get(scope);
    if (existing) return existing;
    const created = { byKey: /* @__PURE__ */ new Map() };
    caches.set(scope, created);
    return created;
};
var stableConfigKey = (config) => {
    if (!config) return "{}";
    const sorted = Object.entries(config).sort(([a], [b]) =>
        a.localeCompare(b),
    );
    return JSON.stringify(Object.fromEntries(sorted));
};
var getCachedClient = (scope, provided, config, factory) => {
    if (provided) {
        const cache2 = getCache(scope);
        cache2.last = provided;
        return provided;
    }
    const cache = getCache(scope);
    const key = stableConfigKey(config);
    const fromKey = cache.byKey.get(key);
    if (fromKey) {
        cache.last = fromKey;
        return fromKey;
    }
    if (!config && cache.last) {
        return cache.last;
    }
    const created = factory(config);
    cache.byKey.set(key, created);
    cache.last = created;
    return created;
};
export {
    AuthClient,
    MemoryStorage,
    buildAuthUrl,
    createAppProbeToken,
    createAuthProbeResponse,
    createHs256Jwt,
    decodeJwtPayload,
    fetchAuthorizations,
    fetchContextAuthorizations,
    getCachedClient,
    getDefaultStorage,
    logoutSession,
    readSession,
    resolveConfig,
    storeSession,
    verifyHs256Jwt,
};
