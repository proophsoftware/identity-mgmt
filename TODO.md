# TODO

## Expand UserTypeSchema
- Set landing pages for verification ok/failed, see Http\VerificationHandler
- Set email templates: verification, reset password, ...

## Add Tenant Workflow

- Add config option (env var) to enable/disable on-the-fly tenant creation
- Create tenant from user registration if allowed and does not exist
    - User becomes $TENANT_ADMIN$ of new tenant
- Create tenant as user with role ($SYSTEM_ADMIN$)

## Role Mgmt
- Role $SYSTEM_ADMIN$ can only be set by superuser or another $SYSTEM_ADMIN$
- Role $TENANT_ADMIN$ can only be set by $SYSTEM_ADMIN$ or another $TENANT_ADMIN$ of the same tenant.
- Only $SYSTEM_ADMIN$ or $TENANT_ADMIN$ can add a new role for a tenant.

## Deactivate Superuser
- Superuser can only register the first $SYSTEM_ADMIN$, further attempts will throw exceptions.
    
## Change User Data

## Change Email (add another identity)

## Change Password

## Reset Password

## Sync Login with Auth Service

## GitHub Login

