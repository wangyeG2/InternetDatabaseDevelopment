# 2026 世界杯主题团队主页

“绿茵前线”是一个以 2026 年世界杯为主题的课程项目。网站采用 Yii2 Advanced 搭建，包含面向访客的前台和仅供管理员使用的后台，提供赛事资料、新闻公告、团队介绍、留言互动、统计图表及课程作业下载。

本仓库的项目任务和课程验收目标见 [task.md](task.md)。应用代码位于 [advanced/](advanced/)，Windows 本地部署使用 [tools/deploy-xampp.bat](tools/deploy-xampp.bat)。

## 功能一览

### 前台

- **赛事专题首页**：展示世界杯主题内容、赛事问答及团队信息。
- **新闻公告**：浏览已发布新闻，按关键词、分类和标签筛选，查看新闻详情。
- **世界杯数据**：按赛事阶段和小组查询赛程、比赛详情及赛果；通过图表查看留言趋势、话题分布、参赛球队洲际分布和历届冠军数据。
- **团队介绍**：查看团队资料、公开成员与职责、项目时间线和相关链接。
- **留言板**：登录后可提交话题留言；留言经管理员审核公开后显示，并参与公开数据统计。
- **作业下载**：从团队作业和个人作业目录列出并下载 ZIP 文件。
- **账号功能**：注册、邮箱验证、登录、退出、重发验证邮件和密码重置。

### 后台

- **管理员访问控制**：后台统一校验登录状态、账号状态和管理员角色。
- **新闻管理**：维护分类及新闻，执行发布、撤回、归档/软删除，管理标签并查看操作历史。
- **团队管理**：维护团队资料、成员、成员职责、项目里程碑和相关链接。
- **留言审核**：查看不同状态的留言，将待审核内容公开或隐藏。
- **用户管理**：查询用户并维护账号状态和角色。

## 技术与数据

| 项目 | 实现 |
|---|---|
| 后端框架 | PHP 8.2.12+、Yii2 Advanced |
| 数据库 | MySQL，数据库名 `worldcup`；结构变更由 Yii 迁移管理 |
| 前端 | Yii Views、Bootstrap 5、本地 JavaScript/CSS 资源 |
| 图表 | ECharts 用于世界杯数据页；留言板也提供话题统计展示 |
| 测试与质量 | Codeception、PHP_CodeSniffer、PHPStan |
| 文件下载 | 固定 `data/team` 和 `data/personal` 目录中的 ZIP 文件 |

当前数据库包含 **14 张业务表**（不含 Yii 的 `migration` 迁移记录表）：

- 用户：`user`
- 新闻：`news_category`、`news`、`news_tag`、`news_tag_link`、`news_revision`
- 团队：`team_profile`、`team_member`、`team_member_duty`、`team_milestone`、`team_link`
- 留言：`message`
- 赛事：`cup_team`、`cup_match`

应用按 MVC 分层：前后台控制器与视图分别维护，共享模型放在 `advanced/common`；新闻和团队的复杂写入逻辑由后台服务层处理。

## 仓库结构

```text
.
├── advanced/                     Yii2 Advanced 应用
│   ├── backend/                  后台控制器、表单、服务、视图及测试
│   ├── common/                   前后台共享模型、配置和组件
│   ├── console/                  命令行控制器和数据库迁移
│   ├── frontend/                 前台控制器、表单、视图、资源及测试
│   ├── environments/             Yii 环境初始化配置模板
│   ├── vendor/                   Composer 依赖
│   └── composer.json              依赖与测试/代码质量命令
├── data/
│   ├── install.sql               早期数据库快照
│   ├── team/                     团队交付资料及团队作业文件
│   └── personal/                 个人作业 ZIP 文件
├── tools/
│   ├── deploy-xampp.bat          Windows/XAMPP 一键初始化脚本
│   ├── read-mail.bat             读取本地开发邮件中的验证链接
│   ├── read-mail.ps1             本地邮件读取脚本
│   └── package_b.py              生成新闻模块源码包及 SHA-256 文件
├── task.md                       项目目标、验收要求和协作安排
└── readme.md                     本项目说明与快速开始
```

前台和后台源码的主要位置：

- 前台控制器：`advanced/frontend/controllers/`
- 前台页面：`advanced/frontend/views/`
- 后台控制器：`advanced/backend/controllers/`
- 后台业务服务：`advanced/backend/services/`
- 共享模型：`advanced/common/models/`
- 数据库迁移：`advanced/console/migrations/`
- 前后台及公共层测试：各应用目录下的 `tests/`

团队文档和作业文件保存在 `data/team/`、`data/personal/`。`data/install.sql` 是早期包含 11 张业务表的快照，不是当前完整数据库结构；新环境请使用下文的一键脚本执行现有 Yii 迁移，不要将该快照导入已有数据库。

## Windows / XAMPP 快速部署

### 环境要求

- Windows 和 XAMPP；XAMPP MySQL 服务已启动。
- PHP **8.2.12 或以上**，启用 `pdo_mysql` 扩展。
- Composer 可从命令行运行。

### 初始化

1. 如 MySQL 用户名、密码或地址与项目默认值不同，先检查并修改 `advanced/environments/dev/common/config/main-local.php` 中的数据库连接。默认数据库名为 `worldcup`；一键脚本提示的 MySQL 凭据必须与该连接配置一致。
2. 在仓库根目录运行：

   ```powershell
   .\tools\deploy-xampp.bat
   ```

   如果 XAMPP 安装目录不是 `C:\xampp`，将安装目录作为参数传入：

   ```powershell
   .\tools\deploy-xampp.bat "D:\xampp"
   ```

3. 按提示输入 MySQL 用户名及必要的密码。脚本会初始化 Yii 本地环境配置、安装 Composer 依赖、创建 `worldcup` 数据库并执行数据库迁移。看到 `Deployment initialization completed.` 表示初始化完成。

> 对已初始化过的项目，检查实际使用的 `advanced/common/config/main-local.php`。脚本不会覆盖已有本地配置。

### 启动前后台

在两个独立的 PowerShell 窗口中运行以下命令；若 XAMPP 不在 `C:\xampp`，请替换 PHP 可执行文件路径。

前台：

```powershell
Set-Location .\advanced
C:\xampp\php\php.exe -S 127.0.0.1:8087 -t frontend\web frontend\web\router.php
```

后台：

```powershell
Set-Location .\advanced
C:\xampp\php\php.exe -S 127.0.0.1:8088 -t backend\web backend\web\router.php
```

访问地址：

- 前台：<http://127.0.0.1:8087/>
- 后台：<http://127.0.0.1:8088/>

### 首次创建管理员

仓库不预置管理员账号。首次使用时：

1. 在前台注册账号。
2. 本地开发环境的验证邮件保存在 `advanced/frontend/runtime/mail/`。在仓库根目录运行 `.\tools\read-mail.bat`，打开输出的验证链接完成激活。
3. 在另一个终端中为已激活账号授予管理员角色：

   ```powershell
   Set-Location .\advanced
   C:\xampp\php\php.exe yii access/grant-admin your_username
   ```

   将 `your_username` 替换为已激活账号的用户名。

4. 使用同一账号登录后台。该命令只会为已激活的账号授予管理员权限。

## 测试与代码质量

在 `advanced/` 目录下可运行 Composer 中定义的检查命令：

```powershell
composer tests
composer cs
composer static
```

测试使用 Codeception；如测试数据库与本地配置不同，请先配置 `advanced/common/config/test-local.php`。

## 团队与课程资料

项目分工按 `task.md` 和代码模块组织：

| 成员 | 主要负责内容 |
|---|---|
| 王烨 | 用户与管理员权限、部署及项目统筹 |
| 阎科霖 | 新闻公告和新闻管理 |
| 赵非凡 | 团队资料、成员展示与维护 |
| 秦东杰 | 留言板、留言审核及数据图表 |

已整理的团队 PDF 文档和交付资料见 [data/team/](data/team/)；个人作业见 [data/personal/](data/personal/)。

## 安全提示

- 不要提交 `*-local.php`、数据库密码、邮件凭据或其他密钥。
- 不要在已有数据库上重新导入 `data/install.sql`；使用 Yii 迁移升级结构。
- 后台仅对有效的管理员账号开放；请勿将管理员凭据分享给他人。
