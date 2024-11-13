export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface App {
    name: string;
    version: string;
    debug: boolean;
    env: string;
    url: string;
    timezone: string;
    locale: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    app: App;
};
