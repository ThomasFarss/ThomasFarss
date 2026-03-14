package com.gamevault.app.utils;

import android.content.Context;
import android.content.SharedPreferences;

public class SessionManager {
    private static final String PREF_NAME = "gamevault_session";
    private static final String KEY_USER_ID = "user_id";
    private static final String KEY_IS_ADMIN = "is_admin";

    private final SharedPreferences preferences;

    public SessionManager(Context context) {
        preferences = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
    }

    public void saveSession(String userId, boolean isAdmin) {
        preferences.edit().putString(KEY_USER_ID, userId).putBoolean(KEY_IS_ADMIN, isAdmin).apply();
    }

    public String getUserId() { return preferences.getString(KEY_USER_ID, null); }
    public boolean isAdminSession() { return preferences.getBoolean(KEY_IS_ADMIN, false); }
    public boolean isLoggedIn() { return getUserId() != null; }
    public void clear() { preferences.edit().clear().apply(); }
}
