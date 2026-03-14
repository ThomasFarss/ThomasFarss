package com.gamevault.app.services;

import com.gamevault.app.models.AccessType;
import com.gamevault.app.models.AppUser;
import com.gamevault.app.models.GameFolder;

public class PermissionService {
    public boolean canAccessFolder(GameFolder folder, AppUser user, boolean passwordValidated) {
        if (folder == null || user == null) return false;

        if (folder.getOwnerId() != null && folder.getOwnerId().equals(user.getUid())) {
            return true;
        }

        switch (folder.getAccessType()) {
            case PUBLIC:
                return true;
            case PRIVATE_PASSWORD:
                return passwordValidated;
            case OWNER_ONLY:
                return false;
            case GROUP_RESTRICTED:
                if (folder.getAllowedGroupIds() == null || user.getGroupIds() == null) return false;
                for (String groupId : user.getGroupIds()) {
                    if (folder.getAllowedGroupIds().contains(groupId)) return true;
                }
                return false;
            default:
                return false;
        }
    }

    public boolean canEditFolder(GameFolder folder, AppUser user) {
        return folder != null && user != null && folder.getOwnerId() != null && folder.getOwnerId().equals(user.getUid());
    }
}
