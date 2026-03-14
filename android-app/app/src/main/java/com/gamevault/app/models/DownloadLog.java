package com.gamevault.app.models;

import java.io.Serializable;
import java.util.Date;

public class DownloadLog implements Serializable {
    private String id;
    private String userId;
    private String userName;
    private String folderId;
    private String gameName;
    private Date downloadedAt;

    public DownloadLog() { downloadedAt = new Date(); }

    public String getId() { return id; }
    public void setId(String id) { this.id = id; }
    public String getUserId() { return userId; }
    public void setUserId(String userId) { this.userId = userId; }
    public String getUserName() { return userName; }
    public void setUserName(String userName) { this.userName = userName; }
    public String getFolderId() { return folderId; }
    public void setFolderId(String folderId) { this.folderId = folderId; }
    public String getGameName() { return gameName; }
    public void setGameName(String gameName) { this.gameName = gameName; }
    public Date getDownloadedAt() { return downloadedAt; }
    public void setDownloadedAt(Date downloadedAt) { this.downloadedAt = downloadedAt; }
}
