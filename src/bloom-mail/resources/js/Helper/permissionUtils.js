// permissionUtils.js

/**
 * Checks if the user has the specified permission.
 *
 * @param {Array} userPermissions - Array of permissions the user has.
 * @param {string} permission - The permission to check.
 * @returns {boolean} - Returns true if the permission is granted, false otherwise.
 */
export function permissionGrant(userPermissions,permission) {
    if (!Array.isArray(userPermissions)) {
        console.error("Invalid permissions array provided.");
        return false;
    }

    return userPermissions.includes(permission);
}
