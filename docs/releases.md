# Releases and Packagist

Version tags are **SemVer only** (no `v` prefix): e.g. `1.0.0`, `2.1.3`. The release workflow runs only on tags matching that pattern.

1. Ensure `main` is green (all CI checks pass).
2. Tag and push:

   ```bash
   git tag 1.0.0
   git push origin 1.0.0
   ```

3. The release workflow runs `composer ci` and creates a GitHub Release.

## Packagist

Configure Packagist’s **GitHub integration** so the package index updates when you push (including tags). Log in to [packagist.org](https://packagist.org/) with GitHub and grant the Packagist GitHub app access to this repository, or add a [manual webhook](https://packagist.org/about) (payload URL, secret with your API token, `push` events). See Packagist’s documentation for details.
