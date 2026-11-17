# 新闻公告数据字典（B）

本节对应 2026 年 11 月 1 日的迁移结果，供 A 合并到团队数据库设计。B 新增五张业务表，引用原有 `user.id`；此外给 `user` 增加 `role VARCHAR(16) NOT NULL DEFAULT 'member'`，取值为 `member/admin`。框架 `migration` 表只记录安装版本。

所有新闻表使用 InnoDB、utf8mb4、utf8mb4_unicode_ci；时间字段为 Unix 秒。原有用户表的字符集沿用框架迁移，本次没有自动转换已有用户数据。

## news_category 新闻分类

| 字段 | MySQL 类型 | 允许空 | 默认与约束 | 含义 |
| --- | --- | --- | --- | --- |
| id | INT | 否 | 自增主键 | 分类编号 |
| name | VARCHAR(60) | 否 | 唯一 | 显示名称 |
| slug | VARCHAR(80) | 否 | 唯一 | 分类标识 |
| description | VARCHAR(255) | 否 | 空字符串 | 分类说明 |
| sort_order | SMALLINT | 否 | 0 | 升序显示，编号用于同序排序 |
| is_active | TINYINT(1) | 否 | 1 | 1 启用，0 停用 |
| created_at | INT | 否 | 应用写入 | 创建时间 |
| updated_at | INT | 否 | 应用维护 | 修改时间 |

默认分类为赛事资讯、团队公告和观赛指南。分类停用后，其新闻不再公开；分类不提供物理删除入口。

## news 新闻

| 字段 | MySQL 类型 | 允许空 | 默认与约束 | 含义 |
| --- | --- | --- | --- | --- |
| id | INT | 否 | 自增主键 | 新闻编号 |
| category_id | INT | 否 | 外键 | 所属分类 |
| author_id | INT | 否 | 外键 | 原始作者，编辑时保持 |
| title | VARCHAR(180) | 否 | 必填 | 标题 |
| slug | VARCHAR(96) | 否 | 唯一 | 新闻标识，可自动生成 |
| summary | VARCHAR(360) | 否 | 空字符串 | 摘要 |
| content | MEDIUMTEXT | 否 | 应用限制 50000 字符 | 纯文本正文 |
| status | SMALLINT | 否 | 0 | 0 草稿，10 发布，20 归档 |
| is_pinned | TINYINT(1) | 否 | 0 | 是否置顶 |
| published_at | INT | 是 | NULL | 最近一次发布所用时间 |
| deleted_at | INT | 是 | NULL | 软删除时间 |
| created_at | INT | 否 | 应用写入 | 创建时间 |
| updated_at | INT | 否 | 应用维护 | 修改时间 |

公开条件同时要求：`status=10`、`deleted_at IS NULL`、`published_at <= 当前时间`、所属分类启用。撤回后状态为草稿、发布时间清空；软删除后状态为归档、写入删除时间并清空发布时间。再次发布草稿时使用新的发布时间。

索引包括唯一 `slug`、`category_id`、`author_id`，以及 `(status, deleted_at, is_pinned, published_at)` 公开列表复合索引。

## news_tag 标签

| 字段 | MySQL 类型 | 允许空 | 默认与约束 | 含义 |
| --- | --- | --- | --- | --- |
| id | INT | 否 | 自增主键 | 标签编号 |
| name | VARCHAR(32) | 否 | 唯一 | 去重、去首尾空白并转小写的标签名 |
| created_at | INT | 否 | 应用写入 | 创建时间 |

每篇新闻最多十个标签。标签可复用，移除某篇新闻的关联不会物理删除标签记录。

## news_tag_link 新闻标签关联

| 字段 | MySQL 类型 | 允许空 | 约束 | 含义 |
| --- | --- | --- | --- | --- |
| news_id | INT | 否 | 联合主键、外键 | 新闻编号 |
| tag_id | INT | 否 | 联合主键、外键 | 标签编号 |

主键为 `(news_id, tag_id)`，防止重复关联；另有 `tag_id` 索引用于按标签筛选新闻。

## news_revision 修改记录

| 字段 | MySQL 类型 | 允许空 | 约束 | 含义 |
| --- | --- | --- | --- | --- |
| id | INT | 否 | 自增主键 | 记录编号 |
| news_id | INT | 否 | 外键 | 对应新闻 |
| editor_id | INT | 否 | 外键 | 本次操作人 |
| operation | VARCHAR(16) | 否 | 应用限定操作名 | create、update、publish、withdraw、delete |
| snapshot | MEDIUMTEXT | 否 | JSON 文本 | 操作后的新闻属性及标签名 |
| created_at | INT | 否 | 应用写入 | 操作时间 |

索引为 `(news_id, created_at)` 和 `editor_id`。历史页面按记录编号倒序分页。正常业务入口只追加历史，不修改已有快照。

## 外键和事务

| 外键关系 | 删除行为 | 更新行为 |
| --- | --- | --- |
| news.category_id → news_category.id | RESTRICT | CASCADE |
| news.author_id → user.id | RESTRICT | CASCADE |
| news_tag_link.news_id → news.id | CASCADE | CASCADE |
| news_tag_link.tag_id → news_tag.id | CASCADE | CASCADE |
| news_revision.news_id → news.id | CASCADE | CASCADE |
| news_revision.editor_id → user.id | RESTRICT | CASCADE |

新闻采用软删除，正常删除不触发物理级联。用户和分类优先采用停用状态，保留历史归属。新闻保存、标签同步和历史追加在一个事务内执行，任一步失败均回滚。

迁移依次为 `m261031_100000_add_user_role`、`m261031_101000_create_news_tables`、`m261101_100000_expand_news_text_capacity`，加上框架原有两项共五项。长文本容量使用单独升级迁移，已有数据库可以按版本顺序升级。
