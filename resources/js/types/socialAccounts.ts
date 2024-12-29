export type SocialAccounts = {
    [platform: string]: SocialAccount[];
};

export interface BaseAccount {
    id: number;
    name: string;
    handle: string;
    profile_image_url: string;
    sociable_id: number;
    sociable_type: string;
    platform_title: string;
    platform_identifier: string;
}
export type SocialAccount = BaseAccount;
