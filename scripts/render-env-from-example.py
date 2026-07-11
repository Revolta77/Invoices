#!/usr/bin/env python3
"""Render .env from .env.example using environment variables."""

from __future__ import annotations

import os
import sys


def render(example_path: str, output_path: str) -> None:
    with open(example_path, encoding="utf-8") as handle:
        lines = handle.read().splitlines()

    rendered: list[str] = []

    for line in lines:
        stripped = line.strip()

        if not stripped or stripped.startswith("#") or "=" not in line:
            rendered.append(line)
            continue

        key, default = line.split("=", 1)
        key = key.strip()
        value = os.environ.get(key)

        if value is None or value == "":
            rendered.append(line)
            continue

        if (
            " " in value
            or "#" in value
            or value.startswith('"')
            or "${" in default
        ):
            escaped = value.replace("\\", "\\\\").replace('"', '\\"')
            rendered.append(f'{key}="{escaped}"')
        else:
            rendered.append(f"{key}={value}")

    with open(output_path, "w", encoding="utf-8") as handle:
        handle.write("\n".join(rendered) + "\n")


if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: render-env-from-example.py <.env.example> <.env>", file=sys.stderr)
        raise SystemExit(1)

    render(sys.argv[1], sys.argv[2])
