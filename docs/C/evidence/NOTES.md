# C 模块验证记录

验证时间：2026-11-16 21:44–21:47（Asia/Shanghai，UTC+8）。环境为本机隔离工作目录，不是课程最终部署环境。

本机 apt 无法访问 Debian 软件源（代理返回 500），改用静态 PHP 8.4.23 CLI（static-php-cli，已含 pdo_mysql、mbstring、openssl、fileinfo 等；**未包含 intl**）和 MariaDB 11.4.8 二进制包。数据库仅监听 127.0.0.1:33079，开发库 `internet_team_c_dev`，测试库 `internet_team_c_test`。这些运行时工具与本地连接不纳入仓库或交付包。代码基线为 `origin/feature/b-news`（`6a4e048`），不是当时尚未合入 B 的旧 `main`（`642f359`）；详见接入说明。

## 已执行并通过（自动功能/安全检查，不是视觉验收）

- 全部新增/修改 PHP 文件语法检查（`php -l`）通过。
- 开发库与测试库均成功应用全部 6 项迁移（含 C 的 `m261116_100000_create_team_tables`）。
- `php yii team/seed` 写入带“示例”标记的展示数据，未创建账号。
- Codeception `--env php-builtin`：**81 tests, 303 assertions，全部通过**（含原有 B 用例及 C 新增前后台功能/单元测试）。
- PHPStan level 5：通过。
- Yii2 phpcs：通过。
- `composer validate --strict --no-check-publish`：通过。
- 本机命令行 HTTP 检查（`curl`，不是浏览器视觉验收）：
  - 访客打开首页、团队介绍、成员页（赵非凡）返回 200；不存在成员 slug 返回 404。
  - 访客访问后台 `team-member/index` 返回 302 到登录页。
  - 普通成员后台登录被拒绝，表单提示 `This account does not have backend access.`（与 B 的后台登录边界一致）。已登录普通成员访问管理路由返回 403 由自动测试覆盖。
  - 管理员可打开并保存团队资料；非法 slug 显示 `.error-summary`；缺少 CSRF 的 POST 返回 400。
  - 保存后前台团队页出现更新后的示例名称，导航含首页、团队介绍、成员、新闻公告、作业下载。
  - 随后将前后台开发服务改绑到 `0.0.0.0:8080/8081` 后，从本机对 `127.0.0.1` 与 `172.30.0.2` 的 `curl` 仍得到首页、团队页、后台登录页 200。这只证明本机进程可达，不能替代浏览器视觉验收。

## 视觉验收未通过（不要把页面写成已验收）

真实浏览器视觉验收**没有完成，也没有通过**。两次尝试均因本环境网络限制失败，且不能绕过该限制，因此不再继续用浏览器重试：

1. 第一次：开发服务只监听 `127.0.0.1`，外部浏览器访问 8080/8081 连接被拒绝。
2. 第二次：服务改绑到 `0.0.0.0` 后，浏览器访问 `172.30.0.2:8080/8081` 收到 HTTP 403（环境网络限制），未能打开页面做视觉检查。

因此：自动功能与安全检查已通过，但视觉验收未完成。没有浏览器视觉回归截图，也没有手机宽度截图。该历史交付在这一状态下推送到远程 `feature/c-team` 供审阅，当时未合并 `main`、未创建 PR。

## 其他未宣称的事项

- 未重新导出全组 `data/install.sql`（仍为 B 快照）。
- D 留言板/动态图形、A 最终部署材料未完成，不能视为全组验收通过。
- 本地预览账号只存在于本机开发库，未写入仓库或交付包。

## 2026-11-17 基线更新（Asia/Shanghai）

2026-11-17（Asia/Shanghai），最新 `origin/main` 为 `e1fd0496acb75787fd66a473ce00a9dcda50284d`（PR #2 合入 B），B tip `6a4e0489b6cd74b1084f5357f21a19ff076f9627` 已是其祖先，二者树相同，无额外文件变化。本地 `integration/c-main-20261117` 已通过 `5a0228bdc2f151b1d90ea29831ccf5734ba397de` 零冲突合入 C，结果树与 C tip 相同，尚未推送。 `data/install.sql` 保留 B 快照。本次仅进行接口静态审查和文档更新，提交后的测试由 supervisor 执行；上面的测试数字是历史记录，视觉验收未通过的记录仍然有效。

## 2026-11-17 本地合入最新 main 后的回归（Asia/Shanghai，UTC+8）

2026-11-17 18:58–18:59，在本地分支 `integration/c-main-20261117`（文档提交 `332aff2` 之后）对同一隔离环境再次执行并全部通过：`php -l`（相对最新 `origin/main` 的 PHP 变更）、开发库与测试库 `migrate`（均已是最新，无待执行迁移）、`composer validate --strict --no-check-publish`、Codeception `--env php-builtin` **81 tests, 303 assertions**、PHPStan level 5、Yii2 phpcs 105 文件。完整输出见仓库外 `/workspace/course-merge-review/test-run.log`。

同日 18:59 对本机已有开发服务做了 `curl` HTTP 检查（不是浏览器视觉验收）：前台首页、团队介绍、新闻公告、作业下载返回 200；成员页 `example-member-a/b/c/d`（王烨、阎科霖、赵非凡、秦东杰）返回 200；不存在成员 slug 返回 404；访客访问后台 `team-member/index` 与 `news/index` 返回 302。记录见 `/workspace/course-merge-review/http-check.log`。

浏览器视觉验收仍未完成、未通过（先前 localhost 连接拒绝，其后访问 `172.30.0.2` 收到环境网络限制的 HTTP 403）。本次未再尝试用浏览器绕过该限制，不能把上述 200 写成页面已视觉验收。
