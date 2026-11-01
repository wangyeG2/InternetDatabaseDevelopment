"""Build the coursework source download from Git's non-ignored file list. Maintainer: B."""
from __future__ import annotations

import hashlib
from pathlib import Path
import subprocess
import zipfile


def main() -> None:
    root = Path(__file__).resolve().parents[1]
    output = root / "data" / "team" / "B_news_source.zip"
    paths = subprocess.check_output(
        ["git", "ls-files", "--cached", "--others", "--exclude-standard", "-z"], cwd=root
    ).decode("utf-8").split("\0")
    excluded = ("advanced/vendor/", "data/team/", "data/personal/", "docs/B/evidence/qa/")
    selected = []
    for path in sorted(set(paths)):
        if (not path or path.startswith(excluded) or "/runtime/" in path
                or "/web/assets/" in path or not (root / path).is_file()):
            continue
        # Environment templates are public source; initialized local configs are private.
        if path.endswith("-local.php") and not path.startswith("advanced/environments/"):
            raise RuntimeError(f"Generated or private file unexpectedly tracked: {path}")
        if (root / path).is_symlink():
            raise RuntimeError(f"Symbolic links are not included in the source archive: {path}")
        (root / path).resolve().relative_to(root)
        selected.append(path)
    output.parent.mkdir(parents=True, exist_ok=True)
    with zipfile.ZipFile(output, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for relative in selected:
            info = zipfile.ZipInfo(relative, date_time=(2026, 10, 31, 0, 0, 0))
            info.compress_type = zipfile.ZIP_DEFLATED
            info.external_attr = 0o100644 << 16
            archive.writestr(info, (root / relative).read_bytes())
    with zipfile.ZipFile(output) as archive:
        if archive.testzip() is not None:
            raise RuntimeError("Source archive verification failed")
    digest = hashlib.sha256(output.read_bytes()).hexdigest()
    output.with_suffix(output.suffix + ".sha256").write_text(f"{digest}  {output.name}\n", encoding="utf-8")
    print(f"Created {output.name}: {len(selected)} files, SHA256 {digest}")


if __name__ == "__main__":
    main()
