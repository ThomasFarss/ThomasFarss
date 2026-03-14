package com.gamevault.app.activities;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.models.DownloadLog;
import com.gamevault.app.models.GameFolder;
import com.gamevault.app.services.FirestoreService;

public class GameDetailActivity extends AppCompatActivity {
    private GameFolder folder;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_game_detail);

        String folderId = getIntent().getStringExtra("folderId");
        if (folderId == null) {
            finish();
            return;
        }

        loadFolder(folderId);
        findViewById(R.id.buttonEditFolder).setOnClickListener(v -> {
            if (folder == null) return;
            Intent i = new Intent(this, EditGameFolderActivity.class);
            i.putExtra("folderId", folder.getId());
            startActivity(i);
        });
        findViewById(R.id.buttonDownload).setOnClickListener(v -> downloadFolder());
    }

    private void loadFolder(String folderId) {
        new FirestoreService().getFolderById(folderId).addOnSuccessListener(documentSnapshot -> {
            folder = documentSnapshot.toObject(GameFolder.class);
            if (folder == null) return;
            ((TextView) findViewById(R.id.textDetailTitle)).setText(folder.getGameName());
            ((TextView) findViewById(R.id.textDetailDescription)).setText(folder.getDescription());
            ((TextView) findViewById(R.id.textDetailMeta)).setText(folder.getCategory() + " • " + folder.getFileSize());
        });
    }

    private void downloadFolder() {
        if (folder == null) return;
        startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(folder.getDownloadLink())));
        DownloadLog log = new DownloadLog();
        log.setFolderId(folder.getId());
        log.setGameName(folder.getGameName());
        new FirestoreService().registerDownload(log);
    }
}
