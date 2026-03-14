package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.models.AccessType;
import com.gamevault.app.models.GameFolder;
import com.gamevault.app.services.AuthService;
import com.gamevault.app.services.FirestoreService;

public class CreateGameFolderActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_create_folder);

        EditText name = findViewById(R.id.editGameName);
        EditText description = findViewById(R.id.editDescription);
        EditText category = findViewById(R.id.editCategory);
        EditText size = findViewById(R.id.editFileSize);
        EditText cover = findViewById(R.id.editCoverUrl);
        EditText link = findViewById(R.id.editDownloadLink);
        EditText password = findViewById(R.id.editFolderPassword);
        Spinner access = findViewById(R.id.spinnerAccessType);
        Button save = findViewById(R.id.buttonSaveFolder);

        access.setAdapter(new ArrayAdapter<>(this, android.R.layout.simple_spinner_dropdown_item, AccessType.values()));

        save.setOnClickListener(v -> {
            GameFolder folder = new GameFolder();
            folder.setGameName(name.getText().toString());
            folder.setDescription(description.getText().toString());
            folder.setCategory(category.getText().toString());
            folder.setFileSize(size.getText().toString());
            folder.setCoverImageUrl(cover.getText().toString());
            folder.setDownloadLink(link.getText().toString());
            folder.setAccessType((AccessType) access.getSelectedItem());
            folder.setPassword(password.getText().toString());
            if (new AuthService().currentUser() != null) {
                folder.setOwnerId(new AuthService().currentUser().getUid());
                folder.setOwnerName(new AuthService().currentUser().getEmail());
            }
            new FirestoreService().saveGameFolder(folder)
                    .addOnSuccessListener(r -> {
                        Toast.makeText(this, R.string.folder_saved, Toast.LENGTH_LONG).show();
                        finish();
                    })
                    .addOnFailureListener(e -> Toast.makeText(this, e.getMessage(), Toast.LENGTH_LONG).show());
        });
    }
}
