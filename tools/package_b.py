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
    selected = sorted({p for p in paths if p and not p.startswith(excluded) and (root / p).is_file()})
    for path in selected:
        if path.endswith("-local.php") or "/runtime/" in path or "/web/assets/" in path:
            raise RuntimeError(f"Generated or private file unexpectedly tracked: {path}")
        (root / path).resolve().relative_to(root)
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
    print(f"Created {output.name}: {len(selected)} files, SHA256 {digest}")


if __name__ == "__main__":
    main()
