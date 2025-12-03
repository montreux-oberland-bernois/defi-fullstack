# Release Guide

This document describes the release process for the Train Routing Application.

## Release Process

The project uses automated releases via GitHub Actions. When a version tag is pushed, the release workflow automatically:

1. Creates a GitHub release with an auto-generated changelog
2. Builds and publishes Docker images to GitHub Container Registry (GHCR)
3. Tags images with semantic versioning

## Creating a New Release

### Prerequisites

- Ensure all changes are merged to the main branch
- All CI/CD checks must pass
- Update `CHANGELOG.md` with the new version and changes

### Steps

1. **Update CHANGELOG.md**

   Add a new version section with the release date and changes:

   ```markdown
   ## [1.0.0] - 2024-12-03

   ### Added
   - Initial release
   - Full-stack train routing application
   - REST API with JWT authentication
   - Vue.js frontend with Vuetify
   ```

2. **Create and Push Version Tag**

   ```bash
   # Create an annotated tag
   git tag -a v1.0.0 -m "Release version 1.0.0"

   # Push the tag to trigger the release workflow
   git push origin v1.0.0
   ```

3. **Monitor Release Workflow**

   - Go to GitHub Actions tab in the repository
   - Watch the "Release" workflow execution
   - Verify both jobs complete successfully:
     - `release` - Creates the GitHub release
     - `docker-release` - Builds and publishes Docker images

4. **Verify Release Artifacts**

   After the workflow completes:

   - **Docker Images**: Verify images are published to GHCR:
     - `ghcr.io/esysc/defi-fullstack/backend:1.0.0`
     - `ghcr.io/esysc/defi-fullstack/backend:1.0`
     - `ghcr.io/esysc/defi-fullstack/backend:1`
     - `ghcr.io/esysc/defi-fullstack/frontend:1.0.0`
     - `ghcr.io/esysc/defi-fullstack/frontend:1.0`
     - `ghcr.io/esysc/defi-fullstack/frontend:1`
     - `ghcr.io/esysc/defi-fullstack/nginx:1.0.0`
     - `ghcr.io/esysc/defi-fullstack/nginx:1.0`
     - `ghcr.io/esysc/defi-fullstack/nginx:1`

## Semantic Versioning

This project follows [Semantic Versioning](https://semver.org/):

- **MAJOR** version (X.0.0): Incompatible API changes
- **MINOR** version (0.X.0): Backwards-compatible functionality additions
- **PATCH** version (0.0.X): Backwards-compatible bug fixes

### Version Bumping Guidelines

- **Patch Release (1.0.1)**: Bug fixes, security patches, minor improvements
- **Minor Release (1.1.0)**: New features, non-breaking API changes
- **Major Release (2.0.0)**: Breaking changes, major refactoring, API redesign

## Release Workflow Details

### Automatic Changelog Generation

The release workflow automatically generates a changelog from git commits:

```yaml
# Format: - commit message (commit hash)
# Example:
- Add user authentication (a1b2c3d)
- Fix route calculation bug (e4f5g6h)
```

For better changelogs, use conventional commit messages:

- `feat:` - New features
- `fix:` - Bug fixes
- `docs:` - Documentation changes
- `chore:` - Maintenance tasks
- `refactor:` - Code refactoring
- `test:` - Test additions/changes
- `perf:` - Performance improvements

### Docker Image Tags

Each release creates multiple Docker image tags:

```bash
# Semantic version tags
ghcr.io/esysc/defi-fullstack/backend:1.2.3  # Full version
ghcr.io/esysc/defi-fullstack/backend:1.2    # Minor version
ghcr.io/esysc/defi-fullstack/backend:1      # Major version
ghcr.io/esysc/defi-fullstack/backend:latest # Latest release (main branch only)
```

This allows users to:

- Pin to exact versions: `backend:1.2.3`
- Get patch updates: `backend:1.2`
- Get minor updates: `backend:1`
- Always use latest: `backend:latest`

## Rollback Procedure

If a release has critical issues:

1. **Delete the GitHub Release**

   ```bash
   # Delete the release on GitHub (UI or API)
   gh release delete v1.0.0
   ```

2. **Delete the Git Tag**

   ```bash
   # Delete local tag
   git tag -d v1.0.0

   # Delete remote tag
   git push origin --delete v1.0.0
   ```

3. **Revert Docker Images**

   Users should pin to the previous stable version:

   ```bash
   docker pull ghcr.io/esysc/defi-fullstack/backend:0.9.0
   docker pull ghcr.io/esysc/defi-fullstack/frontend:0.9.0
   ```

4. **Issue Hotfix**

   - Create a fix on a hotfix branch
   - Merge to main
   - Create a new patch release (v1.0.1)

## Release Checklist

Before creating a release:

- [ ] All tests pass in CI/CD
- [ ] Security vulnerabilities resolved (npm audit, PHPStan)
- [ ] CHANGELOG.md updated with version and changes
- [ ] Documentation updated (if needed)
- [ ] Database migrations tested (if applicable)
- [ ] E2E tests pass
- [ ] Docker images build successfully
- [ ] Version number follows semantic versioning

After creating a release:

- [ ] GitHub release created successfully
- [ ] Docker images published to GHCR
- [ ] Release notes reviewed and edited (if needed)
- [ ] Announcement posted (if applicable)
- [ ] Deployment documentation updated (if needed)

## Troubleshooting

### Release Workflow Fails

**Problem**: The release workflow fails during execution.

**Solutions**:

- Check GitHub Actions logs for specific errors
- Verify `GITHUB_TOKEN` has sufficient permissions
- Ensure tag format matches `v*.*.*` pattern
- Check if previous release with same tag exists

### Docker Image Push Fails

**Problem**: Docker images fail to push to GHCR.

**Solutions**:

- Verify GitHub Container Registry permissions
- Check if package visibility is set correctly
- Ensure repository has container registry enabled
- Verify Docker Buildx is working

### Tag Already Exists

**Problem**: Cannot create tag because it already exists.

**Solutions**:

```bash
# List all tags
git tag -l

# Delete local tag
git tag -d v1.0.0

# Delete remote tag
git push origin --delete v1.0.0

# Recreate tag
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

## Emergency Hotfix Release

For critical bugs requiring immediate release:

1. **Create Hotfix Branch**

   ```bash
   git checkout -b hotfix/v1.0.1 v1.0.0
   ```

2. **Apply Fix**

   ```bash
   # Make necessary changes
   git add .
   git commit -m "fix: critical security vulnerability"
   ```

3. **Merge to Main**

   ```bash
   git checkout main
   git merge --no-ff hotfix/v1.0.1
   ```

4. **Create Hotfix Tag**

   ```bash
   git tag -a v1.0.1 -m "Hotfix release 1.0.1"
   git push origin main
   git push origin v1.0.1
   ```

5. **Clean Up**

   ```bash
   git branch -d hotfix/v1.0.1
   ```

## Contact

For questions about the release process:

- Create an issue in the repository
- Contact the maintainers
- Review the CI/CD workflow documentation

---

Last Updated: December 3, 2024
