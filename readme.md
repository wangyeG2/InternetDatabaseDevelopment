# 2026 世界杯主题团队主页

PHP + Yii2 Advanced + MySQL 课程项目。应用源码位于 `advanced/`，前台与后台分别使用其 `frontend/web`、`backend/web` 目录。

C 最初基于 `feature/b-news`（`6a4e048`）开发，而非旧 main `642f359`；这是开发历史。2026-11-17（Asia/Shanghai），最新 `origin/main` 为 `e1fd0496acb75787fd66a473ce00a9dcda50284d`（PR #2 合入 B），B tip `6a4e0489b6cd74b1084f5357f21a19ff076f9627` 已是其祖先，二者树相同，无额外文件变化。本地 `integration/c-main-20261117` 已通过 `5a0228bdc2f151b1d90ea29831ccf5734ba397de` 零冲突合入 C，结果树与 C tip 相同，尚未推送。 当前应将 C 合入已包含 B 的最新 main，本地集成分支已完成此步。接入细节见 [C 接入说明](docs/C/接入说明.md)。不要用交付包覆盖整个仓库。

先安装锁定依赖，再按 [B 接入与运行说明](docs/B/接入与运行说明.md) 和 [C 接入说明](docs/C/接入说明.md) 配置数据库并启动应用。已有数据库只运行迁移升级，不要重新导入 `data/install.sql`（该文件仍是 B 的空库快照，待 A 在全组集成后重新导出）。

- [B 文档索引](docs/B/README.md)：新闻公告实现报告、数据字典、接口、测试和任务清单。
- [C 文档索引](docs/C/README.md)：团队展示/编辑、视图变量约定、用户手册草稿和接入说明。
- `data/install.sql`：B 阶段空库安装快照；已有数据库使用 Yii 迁移升级。
- `data/team/B_news_source.zip`：B 提供的团队源码下载包，配有 SHA256 校验文件。全组完成后应重新打包。
- `data/personal/`：由各成员放入真实个人作业 ZIP，前台自动提供下载链接。

业务表现状：原有 `user` 和 B 的五张新闻表，C 新增五张团队表，合计十一张业务表。D 的留言板和动态图形、A 的最终部署材料仍待集成，不能视为全组验收完成。
