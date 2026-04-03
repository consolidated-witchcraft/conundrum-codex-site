import createClient from "openapi-fetch";
import type { paths } from "../../resources/typescript/schema/schema";

export function createApiClient() {
    return createClient<paths>({ baseUrl: "" });
}
