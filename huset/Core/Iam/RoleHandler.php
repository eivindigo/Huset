<?php

namespace Core\Iam;

enum RoleHandler: string
{
    case ADMIN = 'b1a7e6f0-3c2d-4e7a-8e2a-1f2c3d4e5f60';
    case USER = 'c2b8f7a1-4d3e-5f8b-9f3b-2g3d4e5f6g71';
    case MANAGER = 'd3c9g8b2-5e4f-6g9c-0g4c-3h4e5f6g7h82';
    case EDITOR = 'e4d0h9c3-6f5g-7h0d-1h5d-4i5f6g7h8i93';
    case VIEWER = 'f5e1i0d4-7g6h-8i1e-2i6e-5j6g7h8i9j04';
    case AUDITOR = 'a6f2j1e5-8h7i-9j2f-3j7f-6k7h8i9j0k15';
    case CONTRIBUTOR = 'b7g3k2f6-9i8j-0k3g-4k8g-7l8i9j0k1l26';
    case SUPPORT = 'c8h4l3g7-0j9k-1l4h-5l9h-8m9j0k1l2m37';
    case GUEST = 'd9i5m4h8-1k0l-2m5i-6m0i-9n0k1l2m3n48';
    case OWNER = 'e0j6n5i9-2l1m-3n6j-7n1j-0o1l2m3n4o59';
}

/**
 * Test if the given role is present in the provided array of RoleHandler enums.
 *
 * @param RoleHandler $role The role to check
 * @param array $roleArray The array of RoleHandler enums
 * @return bool True if the role is in the array, false otherwise or on error
 */
function roleHandlerInArray(RoleHandler $role, array $roleArray): bool
{
    return in_array($role, $roleArray, true);
}

/**
 * Store an array of RoleHandler enums in the session under 'iamRoles'.
 *
 * @param array $roles Array of RoleHandler enums
 * @return void
 */
function storeRolesInSession(array $roles): void
{
    // Only store if all elements are RoleHandler enums
    foreach ($roles as $role) {
        if (!$role instanceof RoleHandler) {
            return;
        }
    }
    // Store as associative array: guid => role name
    $_SESSION['iamRoles'] = [];
    foreach ($roles as $role) {
        $_SESSION['iamRoles'][$role->value] = $role->name;
    }
}

/**
 * Retrieve an array of RoleHandler enums from the session 'iamRoles'.
 *
 * @return array Array of RoleHandler enums, or empty array if not set/invalid
 */
function getRolesFromSession(): array
{
    if (!isset($_SESSION['iamRoles']) || !is_array($_SESSION['iamRoles'])) {
        return [];
    }
    $roles = [];
    foreach (array_keys($_SESSION['iamRoles']) as $roleValue) {
        $role = RoleHandler::tryFrom($roleValue);
        if ($role !== null) {
            $roles[] = $role;
        }
    }
    return $roles;
}
