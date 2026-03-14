package com.gamevault.app.services;

import com.gamevault.app.models.AppUser;
import com.gamevault.app.models.UserRole;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseUser;
import com.google.firebase.firestore.FirebaseFirestore;

public class AuthService {
    private final FirebaseAuth auth;
    private final FirebaseFirestore db;

    public AuthService() {
        auth = FirebaseAuth.getInstance();
        db = FirebaseFirestore.getInstance();
    }

    public Task<?> login(String email, String password) {
        return auth.signInWithEmailAndPassword(email, password);
    }

    public Task<?> register(String name, String email, String password) {
        return auth.createUserWithEmailAndPassword(email, password).continueWithTask(task -> {
            if (!task.isSuccessful() || auth.getCurrentUser() == null) {
                throw task.getException();
            }
            FirebaseUser firebaseUser = auth.getCurrentUser();
            AppUser user = new AppUser();
            user.setUid(firebaseUser.getUid());
            user.setName(name);
            user.setEmail(email);
            user.setRole(UserRole.USER);
            return db.collection("users").document(firebaseUser.getUid()).set(user);
        });
    }

    public Task<Void> forgotPassword(String email) {
        return auth.sendPasswordResetEmail(email);
    }

    public FirebaseUser currentUser() {
        return auth.getCurrentUser();
    }

    public void logout() {
        auth.signOut();
    }
}
