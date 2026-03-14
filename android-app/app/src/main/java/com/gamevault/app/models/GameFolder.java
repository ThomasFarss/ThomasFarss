package com.gamevault.app.models;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

public class GameFolder implements Serializable {
    private String id;
    private String gameName;
    private String description;
    private String category;
    private String coverImageUrl;
    private String fileSize;
    private String downloadLink;
    private AccessType accessType;
    private String password;
    private String ownerId;
    private String ownerName;
    private Date createdAt;
    private int downloadCount;
    private List<String> allowedGroupIds;

    public GameFolder() {
        this.allowedGroupIds = new ArrayList<>();
        this.createdAt = new Date();
    }

    public String getId() { return id; }
    public void setId(String id) { this.id = id; }
    public String getGameName() { return gameName; }
    public void setGameName(String gameName) { this.gameName = gameName; }
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    public String getCategory() { return category; }
    public void setCategory(String category) { this.category = category; }
    public String getCoverImageUrl() { return coverImageUrl; }
    public void setCoverImageUrl(String coverImageUrl) { this.coverImageUrl = coverImageUrl; }
    public String getFileSize() { return fileSize; }
    public void setFileSize(String fileSize) { this.fileSize = fileSize; }
    public String getDownloadLink() { return downloadLink; }
    public void setDownloadLink(String downloadLink) { this.downloadLink = downloadLink; }
    public AccessType getAccessType() { return accessType; }
    public void setAccessType(AccessType accessType) { this.accessType = accessType; }
    public String getPassword() { return password; }
    public void setPassword(String password) { this.password = password; }
    public String getOwnerId() { return ownerId; }
    public void setOwnerId(String ownerId) { this.ownerId = ownerId; }
    public String getOwnerName() { return ownerName; }
    public void setOwnerName(String ownerName) { this.ownerName = ownerName; }
    public Date getCreatedAt() { return createdAt; }
    public void setCreatedAt(Date createdAt) { this.createdAt = createdAt; }
    public int getDownloadCount() { return downloadCount; }
    public void setDownloadCount(int downloadCount) { this.downloadCount = downloadCount; }
    public List<String> getAllowedGroupIds() { return allowedGroupIds; }
    public void setAllowedGroupIds(List<String> allowedGroupIds) { this.allowedGroupIds = allowedGroupIds; }
}
