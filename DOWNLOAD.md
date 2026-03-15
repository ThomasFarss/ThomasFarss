# Download do código (sem binários versionados)

Para manter o repositório limpo, arquivos binários não são versionados.

Se você quiser gerar um pacote `.zip` do projeto localmente, execute:

```bash
cd /workspace/ThomasFarss
python - <<'PY'
import os, zipfile
root='.'
out='GameVault-Downloads.zip'
if os.path.exists(out):
    os.remove(out)
with zipfile.ZipFile(out,'w',zipfile.ZIP_DEFLATED) as z:
    for base, dirs, files in os.walk(root):
        if '.git' in dirs:
            dirs.remove('.git')
        for f in files:
            if f.endswith('.zip') and f == out:
                continue
            p=os.path.join(base,f)
            z.write(p, os.path.relpath(p,root))
print('Gerado:', out)
PY
```
