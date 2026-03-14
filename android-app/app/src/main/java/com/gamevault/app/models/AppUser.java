package com.gamevault.app.models;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

public class AppUser implements Serializable {
    private String uid;
    private String name;
    private String email;
    private UserRole role;
    private boolean blocked;
    private List<String> groupIds;

    public AppUser() {
        role = UserRole.USER;
        blocked = false;
        groupIds = new ArrayList<>();
    }

    public String getUid() { return uid; }
    public void setUid(String uid) { this.uid = uid; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }
    public UserRole getRole() { return role; }
    public void setRole(UserRole role) { this.role = role; }
    public boolean isBlocked() { return blocked; }
    public void setBlocked(boolean blocked) { this.blocked = blocked; }
    public List<String> getGroupIds() { return groupIds; }
    public void setGroupIds(List<String> groupIds) { this.groupIds = groupIds; }
}
