# Releases and Packagist

Version tags are **SemVer only** (no `v` prefix): e.g. `1.0.0`, `2.1.3`. The release workflow runs only on tags matching that pattern.

1. Ensure `main` is green (all CI checks pass).
2. Tag and push:

   ```bash
   git tag 1.0.0
   git push origin 1.0.0
   ```

3. The release workflow will run `composer ci`, create a GitHub Release, and can trigger a Packagist update if secrets are set.

## Packagist

Configure one of these in the repository’s GitHub **Secrets**:

- `PACKAGIST_API_TOKEN`, or
- `PACKAGIST_USERNAME` and `PACKAGIST_TOKEN`

If none are set, the release workflow still runs and logs that the Packagist step was skipped. You can also use Packagist’s GitHub integration (webhook) instead.
