<?php

declare(strict_types=1);

namespace Box\Tests\Fixtures;

class BoxApiFixtures
{
    public static function fileResponse(array $overrides = []): array
    {
        return array_replace_recursive([
            'type'                 => 'file',
            'id'                   => '817696835',
            'sequence_id'          => '3',
            'etag'                 => '3',
            'sha1'                 => '134b65991ed521fcfe4724b7d814ab8ded5185dc',
            'name'                 => 'tigers.jpeg',
            'description'          => 'A photo of tigers',
            'size'                 => 629644,
            'path_collection'      => [
                'total_count' => 2,
                'entries'     => [
                    ['type' => 'folder', 'id' => '0',        'sequence_id' => null, 'etag' => null, 'name' => 'All Files'],
                    ['type' => 'folder', 'id' => '11446498', 'sequence_id' => '1',  'etag' => '1',  'name' => 'Pictures'],
                ],
            ],
            'created_at'           => '2012-12-12T10:55:30-08:00',
            'modified_at'          => '2012-12-12T11:04:26-08:00',
            'trashed_at'           => null,
            'purged_at'            => null,
            'content_created_at'   => '2013-02-04T16:57:52-08:00',
            'content_modified_at'  => '2013-02-04T16:57:52-08:00',
            'created_by'           => ['type' => 'user', 'id' => '17738362', 'name' => 'Sean Rose',  'login' => 'sean@example.com'],
            'modified_by'          => ['type' => 'user', 'id' => '17738362', 'name' => 'Sean Rose',  'login' => 'sean@example.com'],
            'owned_by'             => ['type' => 'user', 'id' => '17738362', 'name' => 'Sean Rose',  'login' => 'sean@example.com'],
            'shared_link'          => null,
            'parent'               => ['type' => 'folder', 'id' => '11446498', 'sequence_id' => '1', 'etag' => '1', 'name' => 'Pictures'],
            'item_status'          => 'active',
        ], $overrides);
    }

    public static function folderResponse(array $overrides = []): array
    {
        return array_replace_recursive([
            'type'              => 'folder',
            'id'                => '11446498',
            'sequence_id'       => '1',
            'etag'              => '1',
            'name'              => 'Pictures',
            'created_at'        => '2012-12-12T10:53:43-08:00',
            'modified_at'       => '2012-12-12T11:15:04-08:00',
            'description'       => 'A collection of photos',
            'size'              => 629644,
            'path_collection'   => [
                'total_count' => 1,
                'entries'     => [
                    ['type' => 'folder', 'id' => '0', 'sequence_id' => null, 'etag' => null, 'name' => 'All Files'],
                ],
            ],
            'created_by'        => ['type' => 'user', 'id' => '17738362', 'name' => 'Sean Rose', 'login' => 'sean@example.com'],
            'modified_by'       => ['type' => 'user', 'id' => '17738362', 'name' => 'Sean Rose', 'login' => 'sean@example.com'],
            'owned_by'          => ['type' => 'user', 'id' => '17738362', 'name' => 'Sean Rose', 'login' => 'sean@example.com'],
            'shared_link'       => null,
            'folder_upload_email' => null,
            'parent'            => ['type' => 'folder', 'id' => '0', 'sequence_id' => null, 'etag' => null, 'name' => 'All Files'],
            'item_status'       => 'active',
            'item_collection'   => ['total_count' => 0, 'entries' => []],
        ], $overrides);
    }

    public static function userResponse(array $overrides = []): array
    {
        return array_replace_recursive([
            'type'            => 'user',
            'id'              => '17738362',
            'name'            => 'Sean Rose',
            'login'           => 'sean@example.com',
            'created_at'      => '2012-03-26T15:43:07-07:00',
            'modified_at'     => '2012-12-12T11:34:29-08:00',
            'language'        => 'en',
            'timezone'        => 'Africa/Banjul',
            'space_amount'    => 5368709120,
            'space_used'      => 2377016,
            'max_upload_size' => 2147483648,
            'status'          => 'active',
            'job_title'       => 'Employee',
            'phone'           => '5555555555',
            'address'         => '555 Box Lane',
            'avatar_url'      => 'https://www.box.com/api/avatar/large/17738362',
        ], $overrides);
    }

    public static function groupResponse(array $overrides = []): array
    {
        return array_replace_recursive([
            'type'        => 'group',
            'id'          => '189108',
            'name'        => 'All employees',
            'created_at'  => '2013-05-16T15:27:16-07:00',
            'modified_at' => '2013-05-16T15:27:16-07:00',
        ], $overrides);
    }

    public static function collaborationResponse(array $overrides = []): array
    {
        return array_replace_recursive([
            'type'             => 'collaboration',
            'id'               => '14176246',
            'created_by'       => ['type' => 'user', 'id' => '4276790', 'name' => 'David Lee',  'login' => 'david@example.com'],
            'created_at'       => '2011-11-29T12:56:35-08:00',
            'modified_at'      => '2012-09-11T10:15:23-07:00',
            'expires_at'       => null,
            'status'           => 'accepted',
            'accessible_by'    => ['type' => 'user', 'id' => '755492',  'name' => 'Simon Tan', 'login' => 'simon@example.com'],
            'role'             => 'editor',
            'acknowledged_at'  => '2011-11-29T12:59:40-08:00',
            'item'             => ['type' => 'folder', 'id' => '11446498', 'sequence_id' => '1', 'etag' => '1', 'name' => 'Pictures'],
        ], $overrides);
    }

    public static function groupMembershipResponse(array $overrides = []): array
    {
        return array_replace_recursive([
            'type'        => 'group_membership',
            'id'          => '1560354',
            'user'        => ['type' => 'user',  'id' => '755492', 'name' => 'Simon Tan',    'login' => 'simon@example.com'],
            'group'       => ['type' => 'group', 'id' => '189108', 'name' => 'All employees'],
            'role'        => 'member',
            'created_at'  => '2013-05-16T15:27:16-07:00',
            'modified_at' => '2013-05-16T15:27:16-07:00',
        ], $overrides);
    }

    public static function userListResponse(?array $entries = null): array
    {
        if ($entries === null) {
            $entries = [
                self::userResponse(),
                self::userResponse(['id' => '23847634', 'name' => 'Alexis Nolan', 'login' => 'alexis@example.com']),
            ];
        }

        return [
            'total_count' => count($entries),
            'entries'     => $entries,
        ];
    }

    public static function groupListResponse(?array $entries = null): array
    {
        if ($entries === null) {
            $entries = [
                self::groupResponse(),
                self::groupResponse(['id' => '200001', 'name' => 'Managers']),
            ];
        }

        return [
            'total_count' => count($entries),
            'entries'     => $entries,
        ];
    }
}
