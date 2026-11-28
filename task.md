
# 2026世界杯主题团队主页 —— 项目整体手册
> **队伍名称**：XXX队（待定）
>  **队伍成员**：王烨（组长）、阎科霖、赵非凡、秦东杰 
>  **DDL**：**2026.11.28**（**尽早完成，便于联系助教**）
> **技术栈**：PHP + Yii2 + MySQL
> **工作平台**：vscode（或其他IDE）+Github
> **tips**：AI这东西大家都知道怎么用的（滑稽）
---
## 目录
1. [工程目标](#一工程目标)
2. [验收清单](#二验收清单)
3. [技术架构](#三技术架构mvc-分层)
4. [人员分工](#四人员分工)
5. [VS Code + GitHub 工作流](#五vs-code--github-工作流)
6. [一些补充](#六一些补充)
---
## 一、工程目标
### 1.1 总目标
在 1 个月内，四人协作完成一个 **2026 世界杯体育主题的团队主页网站**（Yii2 框架，前后端完整），高质量交付课程要求的全部代码与六份团队文档，并顺利完成项目展示。
| 端 | 功能 |
|---|---|
| 前台 | 团队及个人展示、新闻公告浏览、留言板 |
| 后台 | 团队/个人信息编辑、新闻发布、留言管理、用户登录与管理 |
### 1.2 质量目标
- 数据库 **> 10 张表**
- 每位组员 **至少编写 M / V / C 三层各一个文件**
- 统一命名规范、每个 PHP 文件带注释头
- 代码结构符合 MVC 分层：View 不写业务逻辑，Model 不输出 HTML，Controller 只做调度
### 1.3 协作目标
前后端尽量保持同步开发，并保证开发流程与Github Workflow对齐。

## 二、验收清单
### 2.1 项目主题
- 体育主题——**2026 世界杯**
- 团队主页含前后台功能，数据库表 **> 10 张**，且包含动态图形展示
### 2.2 团队作业（六份文档）
| # | 文档 | 内容要求 | 命名格式 |
|---|---|---|---|
| 1 | 需求文档 | 项目需求说明 | `队名_需求文档(学号1_学号2_学号3_学号4).pdf` |
| 2 | 设计文档 | 数据库设计、模块图、流程图、数据库字典及设计图 | `队名_设计文档(…).pdf` |
| 3 | 实现文档 | 实现过程及结果：任务分工过程（Tower 截图）、Git 提交记录（整合为gitlog.txt）、主要代码及目录展示 | `队名_实现文档(…).pdf` |
| 4 | 用户手册 | 自定义（覆盖前台+后台全部功能） | `队名_用户手册(…).pdf` |
| 5 | 部署文档 | 清晰描述部署还原流程，包括一键部署 | `队名_部署文档(…).pdf` |
| 6 | 项目展示 PPT | 包含小组分工，组长、组员对数据表设计、代码、文档的分工及工作量分配 | `队名_项目展示(…).ppt` |
> 注：组长有团队加成分数（团队整体好为正，整体不好为负）。
> 尽力完成即可，力所不能及之处在群内协调。
### 2.3 编码要求
1. **统一命名规范**
2. **每个 PHP 文件加注释头**
3. 数据库抽取 SQL 文件，放 `/data/install.sql`
4. 所有团队工作放 `/data/team/` 文件夹下
5. 所有个人作业均放在`/data/personal/`文件夹下
6. 项目页面提供团队和个人作业的下载链接
7. **每个组员至少编写MVC三层各一个文件**

### 2.4 指定团队合作工具
- 代码版本控制：**Git**
- 项目管理工具：**Tower**

### 2.5 最终提交
项目完成后，进行内部验收，随后与助教进行沟通，择期进行项目演示。项目演示结束后，组长于**2026.12.1**前将所有文档和大作业打包提交至**nkdbis_homework@163.com**。
## 三、技术架构：MVC 分层
### 3.1 分层原则（Yii2）
```
Controller 接收请求 → Model 处理数据 → View 显示页面
```
| 层 | 职责 | 禁止事项 |
|---|---|---|
| **M** Model | 查询/增删改数据库、定义数据结构、验证格式、业务逻辑（密码加密等） | 不写 HTML、不直接面向用户 |
| **V** View | HTML / CSS / JS、展示 Controller 传来的变量 | 不含复杂逻辑、数据库操作、业务计算 |
| **C** Controller | 接收 HTTP 请求、决定调用哪个 Model、把数据传给 View、返回页面 | 不显示页面、不处理数据，只协调 |
### 3.2 目录结构（Yii2 典型）
```
controllers/          ← C 层
    SiteController.php
    UserController.php / NewsController.php / TeamController.php / MessageController.php
models/               ← M 层
    User.php / News.php / Team.php / Message.php ...
views/                ← V 层
    site/  user/  news/  team/  message/
web/                  ← 静态资源、图表 JS等
data/
    install.sql       ← 数据库导出
    team/             ← 团队作业打包
    personal/         ← 个人作业
docs/                 ← 六份文档
```
## 四、人员分工
| 成员 | 角色定位 | MVC 功能切片（三层各 ≥1 文件） | 文档职责 | 工程任务 | 加分项 |
|---|---|---|---|---|---|
| **王烨（组长）** | 后端开发 + 总协调 | 用户登录与管理：User 模型 + 登录/注册/权限 Controller + 登录视图 | 部署文档起草；设计文档起草；项目终审核查；PPT 编写与主讲 | 数据库设计与数据字典、命名规范与注释头模板、联系助教 | **一键部署脚本**|
| **阎科霖** | 后端开发 | 新闻公告发布：News 模型 + NewsController + 列表/详情视图 | 实现文档起草；设计文档的模块图/流程图部分 | 路由表维护、Tower 看板管理、集成期测试用例设计与执行 | — |
| **赵非凡** | 前端开发  | 团队信息展示与编辑：Team 模型 + TeamController + 展示/编辑视图 | 用户手册起草| 视图变量契约起草、全站 layout/导航模板 | — |
| **秦东杰** | 前端开发 | 留言板及留言管理：Message 模型 + MessageController + 留言/管理视图 | 需求文档起草 | 响应式样式、表单交互校验 | **动态图形展示** |


## 五、VS Code + GitHub 工作流
### 5.1 一次性环境搭建
VS Code 安装扩展：
   - GitHub Pull Requests
   - Git Graph
   - Live Share

Github内部，组长创建初始化仓库，主分支为main，作为唯一默认分支，并启用分支保护。

### 5.2 分支模型与保护规则

- main：主分支，包含了所有最新的项目进度
- feature/<姓名>：个人工作分支，贯穿开发期

当main分支开启分支保护后，所有对于main分支的推送均被视为不合法，予以拒绝，必须通过PR流程进行与main分支的合并。

### 5.3 工作标准流程
```bash
#首次工作
git checkout main && git pull
git checkout -b feature/<姓名>        # 首次拉取后建立个人工作分支

#后续日常流程
git checkout feature/<姓名>   #保持在各自工作分支
git fetch origin  #同步远程（每日执行）
git merge origin/main #拉取并将最新主干合并入工作分支

# 如存在冲突，先解决，解决后进行后续工作

#阶段工作完成后
git add .
git commit -m "xxx"   # 提交
git push origin feature/<姓名>

# 前/后端内部完成对接后，发起 PR
#    VS Code → GitHub Pull Requests  → Create Pull Request
#    New pull request 页面右侧栏 → Reviewers → 输入框搜索成员用户名→ 选中组长，组长执行review
#    组长对应工作内容由后端内部审定
#    如果Approve → Create a merge commit 合并
#    → 不要点 Delete branch，保留自己的工作分支

# ⑥ 任务全部完成后统一销毁个人分支
git checkout main && git pull
git branch -d feature/<姓名>
git push origin --delete feature/<姓名>
```
### 5.4 冲突处理
```bash
git checkout feature/xxx
git merge main
# VS Code 内置合并编辑器逐个解决 <<<<<<< 标记 → 提交 → push → 重新请求评审
```
### 5.5 注意事项
- 数据库密码、邮箱授权、apikey等敏感信息**禁止入仓**
- 所有工程问题在群里及时沟通
- 代码框架等可以参考github往届学长，禁止大篇幅抄袭
- 前端UI实现极力避免过度AI风格，不利于实际展示，可以选择借鉴经典模板库

## 六、一些补充
此处实际补充的内容是关于Tower的：
#### Tower 侧
1. 注册 tower.im，创建团队「XXX队」，邀请 3 名组员；
2. 新建项目「2026世界杯团队主页」，类型选**看板项目**；
3. 建立看板清单（从左至右，与 Git 状态一一对应）：


| 看板清单 | 含义 | 对应 Git 状态 |
|---|---|---|
| 待办 | 已拆解、未开工 | 无分支 |
| 开发中 | 已开工 | `feature/<姓名>` 有 commit |
| 待评审 | PR 已发起 | PR open，等待组长 review |
| 返修中 | PR 被 changes requested | 卡片从「待评审」拖回，修改后重新 PR |
| 已完成 | PR 已合并 | PR merged，卡片已回写 commit hash |

可参考如下文档，也可以询问AI：
1. https://tower.im/guides
2. https://blog.csdn.net/wildboy2001/article/details/10047903
3. https://blog.csdn.net/tao_wei162/article/details/84855693
4. https://www.jianshu.com/p/1379fda207de