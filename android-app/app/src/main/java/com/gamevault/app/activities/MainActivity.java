package com.gamevault.app.activities;

import android.content.Intent;
import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.gamevault.app.R;
import com.gamevault.app.adapters.GameFolderAdapter;
import com.gamevault.app.models.GameFolder;
import com.gamevault.app.services.FirestoreService;
import com.google.android.material.floatingactionbutton.FloatingActionButton;

import java.util.ArrayList;
import java.util.List;

public class MainActivity extends AppCompatActivity {
    private final List<GameFolder> folders = new ArrayList<>();
    private GameFolderAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        RecyclerView recycler = findViewById(R.id.recyclerFolders);
        recycler.setLayoutManager(new LinearLayoutManager(this));
        adapter = new GameFolderAdapter(folder -> {
            Intent i = new Intent(this, GameDetailActivity.class);
            i.putExtra("folderId", folder.getId());
            startActivity(i);
        });
        recycler.setAdapter(adapter);

        FloatingActionButton fabCreate = findViewById(R.id.fabCreateFolder);
        fabCreate.setOnClickListener(v -> startActivity(new Intent(this, CreateGameFolderActivity.class)));

        findViewById(R.id.buttonProfile).setOnClickListener(v -> startActivity(new Intent(this, ProfileActivity.class)));
        findViewById(R.id.buttonAdmin).setOnClickListener(v -> startActivity(new Intent(this, AdminPanelActivity.class)));
        loadFolders();
    }

    private void loadFolders() {
        new FirestoreService().getFoldersQuery().get().addOnSuccessListener(queryDocumentSnapshots -> {
            folders.clear();
            folders.addAll(queryDocumentSnapshots.toObjects(GameFolder.class));
            adapter.submitList(folders);
        });
    }
}
