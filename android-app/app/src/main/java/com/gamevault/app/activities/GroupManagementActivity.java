package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.models.UserGroup;
import com.gamevault.app.services.FirestoreService;

public class GroupManagementActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_group_management);

        EditText name = findViewById(R.id.editGroupName);
        EditText description = findViewById(R.id.editGroupDescription);
        Button create = findViewById(R.id.buttonCreateGroup);

        create.setOnClickListener(v -> {
            UserGroup group = new UserGroup();
            group.setName(name.getText().toString());
            group.setDescription(description.getText().toString());
            new FirestoreService().saveGroup(group)
                    .addOnSuccessListener(r -> Toast.makeText(this, R.string.group_saved, Toast.LENGTH_LONG).show())
                    .addOnFailureListener(e -> Toast.makeText(this, e.getMessage(), Toast.LENGTH_LONG).show());
        });
    }
}
