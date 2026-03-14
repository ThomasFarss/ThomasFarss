package com.gamevault.app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.gamevault.app.R;
import com.gamevault.app.models.GameFolder;

import java.util.ArrayList;
import java.util.List;

public class GameFolderAdapter extends RecyclerView.Adapter<GameFolderAdapter.GameFolderViewHolder> {

    public interface OnFolderClickListener {
        void onFolderClick(GameFolder folder);
    }

    private final List<GameFolder> folders = new ArrayList<>();
    private final OnFolderClickListener clickListener;

    public GameFolderAdapter(OnFolderClickListener clickListener) {
        this.clickListener = clickListener;
    }

    public void submitList(List<GameFolder> newFolders) {
        folders.clear();
        folders.addAll(newFolders);
        notifyDataSetChanged();
    }

    @NonNull
    @Override
    public GameFolderViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_game_folder, parent, false);
        return new GameFolderViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull GameFolderViewHolder holder, int position) {
        GameFolder folder = folders.get(position);
        holder.title.setText(folder.getGameName());
        holder.subtitle.setText(folder.getCategory() + " • " + folder.getFileSize());
        holder.owner.setText(holder.itemView.getContext().getString(R.string.by_owner, folder.getOwnerName()));

        Glide.with(holder.cover.getContext())
                .load(folder.getCoverImageUrl())
                .placeholder(R.drawable.ic_app_logo)
                .into(holder.cover);

        holder.itemView.setOnClickListener(v -> clickListener.onFolderClick(folder));
    }

    @Override
    public int getItemCount() {
        return folders.size();
    }

    static class GameFolderViewHolder extends RecyclerView.ViewHolder {
        TextView title, subtitle, owner;
        ImageView cover;

        public GameFolderViewHolder(@NonNull View itemView) {
            super(itemView);
            title = itemView.findViewById(R.id.textGameTitle);
            subtitle = itemView.findViewById(R.id.textGameSubtitle);
            owner = itemView.findViewById(R.id.textGameOwner);
            cover = itemView.findViewById(R.id.imageCover);
        }
    }
}
