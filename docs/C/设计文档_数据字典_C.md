# C 设计与数据字典

C 新增5张业务表；与原 user + B 的5张新闻表合计达到11张（不计migration记录表）。迁移为 `m261116_100000_create_team_tables`，仅新增，不修改现有表/数据，不插入账号。全部InnoDB、utf8mb4_unicode_ci，时间戳为Unix秒。正文为纯文本，TEXT在应用层限制12000字符，兼容多字节字符容量。

## 表结构

| 表 | 字段（类型、限制/默认） |
| --- | --- |
| team_profile | id PK自增；name VARCHAR120、slug VARCHAR80、motto VARCHAR180、summary VARCHAR500、body TEXT、theme_note VARCHAR500；is_current nullable TINYINT默认NULL；updated_by nullable INT；created_at、updated_at INT必填 |
| team_member | id PK自增；name VARCHAR60、slug VARCHAR80、module_code VARCHAR1、role_title VARCHAR120、duty VARCHAR255、bio TEXT；user_id nullable INT；sort_order SMALLINT默认0；is_public TINYINT默认0；created_at、updated_at INT |
| team_member_duty | id PK自增；member_id INT必填；title VARCHAR120、detail VARCHAR1000；sort_order SMALLINT默认0 |
| team_milestone | id PK自增；title VARCHAR180、happened_on VARCHAR10（应用验证YYYY-MM-DD）、summary VARCHAR1000；is_example TINYINT默认0、sort_order SMALLINT默认0、is_public TINYINT默认0；created_at、updated_at INT |
| team_link | id PK自增；label VARCHAR120、url VARCHAR2048；sort_order SMALLINT默认0、is_public TINYINT默认0；created_at、updated_at INT |

除明确可空字段外均NOT NULL；短文本数据库默认空串，body/bio必需由应用赋值。应用必填与长度详见接口约定。无学生学号、邮箱、照片路径等个人资料字段。

## 索引与关系

- profile.slug、member.slug各有唯一索引。
- profile.is_current唯一索引，CHECK限制为NULL或1；MySQL8执行约束，允许多个非当前NULL行、最多一个1。后台仅编辑当前行。
- member/milestone/link各有 `(is_public, sort_order)` 索引，公开排序加id稳定次序。
- duty有 `(member_id, sort_order)` 索引。
- profile.updated_by → user.id，member.user_id → user.id；显式索引，ON DELETE/UPDATE RESTRICT，关联用户不能物理删除，应停用账号。
- duty.member_id → member.id；显式索引，ON DELETE CASCADE、ON UPDATE RESTRICT。业务界面不物理删除成员。

## MVC 数据流

前台TeamController → 共享TeamProfile/TeamMember/TeamMilestone/TeamLink查询 → 纯文本视图。后台应用AdminAccess → 对应控制器 → Team*Form（RequestForm标量防护 + AR规则代理验证） → TeamService再次鉴权 → 事务保存。职责采用一对多替换式维护，成员及职责失败一起回滚。数据库唯一约束处理并发首次创建时防止双当前资料；冲突请求可能得到数据库异常而不会覆盖他人数据，重新打开当前资料再编辑。

TeamAsset使用独立样式，NewsAsset保持原样。未提供用户、新闻、留言或动态图形的数据实现。迁移safeDown明确拒绝自动删表，以保留资料；回退代码前应由维护者备份并另行评估数据库处理。
