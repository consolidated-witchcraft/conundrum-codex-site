import createClient from "openapi-fetch";
import type { paths } from "../schema/schema";
export function createApiClient() {
    const env: string  = 'process.env.APP_ENV';
    const apiBaseUrl = (env == '"dev"') ? 'http://localhost:8089' : 'https://conundrumcodex.com';
    return createClient<paths>({ baseUrl: apiBaseUrl });
}
