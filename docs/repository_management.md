# Repository Management Playbook

This playbook consolidates the operational tasks needed to keep the Ning2.0 source tree
in sync with upstream contributions while planning longer-term repository boundaries. It
builds on the root `AGENTS.md` expectations and the modernization roadmap described in the
README.

## Pull request visibility checks

1. Run `git remote -v` to confirm the remotes the local checkout can see. The default
   `origin` remote should point at `https://github.com/Elshara/Ning2.0.git`.
2. Execute `git remote update origin` before auditing open pull requests so the local
   refs stay current.
3. List every pull request branch that the master branch can inspect with:
   ```bash
   git ls-remote --heads origin 'pull/*/head'
   ```
   The left-hand hash identifies the tip commit of each pull request. Capture anything
   unexpected in the work log so future merges can account for it.
4. Create an ephemeral local branch for deeper review with:
   ```bash
   git fetch origin pull/<id>/head:review/pr-<id>
   git switch review/pr-<id>
   ```
   Inspect the diff against `master`, verify automated checks, and switch back to
   `master` (or `work`) once validation completes.
5. After merging a pull request locally, push the merge commit upstream and close the
   GitHub pull request. Without API access from this environment, close the request in
   the GitHub web UI or via the maintainer workflow once the merge is visible on `master`.

## Tracking auxiliary repositories

* Catalogue additional code sources in `.git/config` using descriptive remote names,
  for example `upstream-legacy` for the historical Ning dumps or `integration-sandbox`
  for feature spikes. Add them with:
  ```bash
  git remote add upstream-legacy https://github.com/<org>/<repo>.git
  git fetch upstream-legacy
  ```
* Use `git worktree add` when the auxiliary repository should be explored alongside the
  main tree. This keeps experiments isolated without polluting `master`.
* Document why each remote exists inside `docs/porting_assessment.md` so future agents
  understand which modules depend on external history.
* When importing code, prefer merge commits over rebases so GitHub preserves authorship
  and the pull request history.

## Repository segmentation roadmap

| Focus area            | Current paths               | Motivation                                                      | Near-term actions |
|-----------------------|-----------------------------|-----------------------------------------------------------------|-------------------|
| Setup wizard          | `setup/`, `setup/src/`      | The installer evolves faster than the runtime and benefits from | Extract shared    |
|                       |                             | independent release cadences and dedicated integration tests.   | validation logic  |
| Legacy runtime core   | `lib/NF/`, `bootstrap.php`  | Modernizing the runtime requires aggressive refactors. Housing  | Continue moving   |
|                       |                             | it in its own repository clarifies ownership and enables strict | URL helpers into  |
|                       |                             | semantic versioning.                                            | `lib/NF/Url/*`.   |
| Widget catalogue      | `widgets/`, `assets/`       | Widgets have unique dependencies and front-end assets. A        | Identify widgets  |
|                       |                             | dedicated repository simplifies theme development and CI.       | ready for export. |
| Legacy admin tooling  | `tools/`, `tests/`          | CLI utilities and audits are shared across deployments. Splitting| Build standalone  |
|                       |                             | them into a tooling repository allows package-style distribution.| Composer package. |

The separation plan above keeps the monolith workable while highlighting surfaces that
warrant deeper investment. Record progress inside the root Expected Work Plan.

## Upstream pull request integration log

| Pull request | Status                                                     | Follow-up |
|--------------|-------------------------------------------------------------|-----------|
| #12          | Changes merged into `work` and ready to live on `master`.   | Close on GitHub after verifying the merge commit is published. |
| #14          | Changes merged into `work` and ready to live on `master`.   | Close on GitHub after verifying the merge commit is published. |

When authoring new changes that build on these pull requests, create fresh branches from
`master` so history remains linear. Reference this table in commit messages whenever
relevant work concludes.
