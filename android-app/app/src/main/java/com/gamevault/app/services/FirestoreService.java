package com.gamevault.app.services;

import com.gamevault.app.models.AppUser;
import com.gamevault.app.models.DownloadLog;
import com.gamevault.app.models.GameFolder;
import com.gamevault.app.models.UserGroup;
import com.google.android.gms.tasks.Task;
import com.google.firebase.firestore.DocumentSnapshot;
import com.google.firebase.firestore.FirebaseFirestore;
import com.google.firebase.firestore.Query;

public class FirestoreService {
    private final FirebaseFirestore db = FirebaseFirestore.getInstance();

    public Task<Void> saveGameFolder(GameFolder folder) {
        if (folder.getId() == null) {
            folder.setId(db.collection("folders").document().getId());
        }
        return db.collection("folders").document(folder.getId()).set(folder);
    }

    public Task<Void> deleteGameFolder(String folderId) {
        return db.collection("folders").document(folderId).delete();
    }

    public Query getFoldersQuery() {
        return db.collection("folders").orderBy("createdAt", Query.Direction.DESCENDING);
    }

    public Task<DocumentSnapshot> getFolderById(String folderId) {
        return db.collection("folders").document(folderId).get();
    }

    public Task<DocumentSnapshot> getUserById(String userId) {
        return db.collection("users").document(userId).get();
    }

    public Query getAllUsers() {
        return db.collection("users").orderBy("name");
    }

    public Task<Void> saveUser(AppUser user) {
        return db.collection("users").document(user.getUid()).set(user);
    }

    public Query getAllGroups() {
        return db.collection("groups").orderBy("name");
    }

    public Task<Void> saveGroup(UserGroup group) {
        if (group.getId() == null) {
            group.setId(db.collection("groups").document().getId());
        }
        return db.collection("groups").document(group.getId()).set(group);
    }

    public Query getDownloadLogs() {
        return db.collection("downloads").orderBy("downloadedAt", Query.Direction.DESCENDING);
    }

    public Task<Void> registerDownload(DownloadLog log) {
        if (log.getId() == null) {
            log.setId(db.collection("downloads").document().getId());
        }
        return db.collection("downloads").document(log.getId()).set(log);
    }
}
