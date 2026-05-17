# Packagist Rebrand Guide

Step-by-step instructions for renaming the GitHub repository and submitting the new Packagist package at v1.0.0 release time.

This guide assumes the new package name and repo URL are known. Do not begin until Step 17 (v1 Release Readiness) is complete and the release tag is ready to push.

---

## Prerequisites

- The v1.0.0 release branch is merged and the tag is ready.
- You have admin access to the GitHub repository.
- You have a Packagist account linked to the repository.

---

## Step 1 — Rename the GitHub repository

1. Go to **Settings → General** on the GitHub repository page.
2. Under **Repository name**, enter the new name (e.g. `php-box-sdk`).
3. Click **Rename**. GitHub automatically creates a redirect from the old URL.

---

## Step 2 — Update the git remote locally

```bash
git remote set-url origin <new-github-url>
git remote -v
```

Verify the new URL is correct before pushing.

---

## Step 3 — Update `composer.json`

Update the following fields to use the new package name and repo URL:

```json
{
    "name": "chancegarcia/php-box-sdk",
    "homepage": "<new-github-url>",
    "support": {
        "docs": "https://developer.box.com/reference",
        "source": "<new-github-url>",
        "issues": "<new-github-url>/issues"
    }
}
```

Commit and push this change to the release branch before tagging.

---

## Step 4 — Update README.md CI badge

The CI badge URL hardcodes the old repo path:

```markdown
[![CI](https://github.com/<old-org>/<old-repo>/actions/workflows/ci.yml/badge.svg)](...)
```

Replace both the image URL and the link URL with the new repo path.

---

## Step 5 — Search for remaining hardcoded references

```bash
grep -r "chancegarcia/box.net-v2api-sdk\|chancegarcia/box-api-v2-sdk" docs/ README.md CHANGELOG.md
```

Update any remaining references found.

---

## Step 6 — Push the v1.0.0 tag

```bash
git tag v1.0.0
git push origin v1.0.0
```

---

## Step 7 — Submit to Packagist

1. Log in to [packagist.org](https://packagist.org).
2. Click **Submit** and enter the new GitHub repository URL.
3. Packagist will crawl the repo and create the new package entry.

If the old package already exists on Packagist under the old name, mark it as **abandoned** and point consumers to the new package name:
1. Go to the old package page on Packagist.
2. Click **Edit** → **Mark as abandoned**.
3. Set the replacement package name to `chancegarcia/php-box-sdk`.

---

## Step 8 — Verify

```bash
composer require chancegarcia/php-box-sdk:^1.0
```

Confirm the correct version resolves and installs.
