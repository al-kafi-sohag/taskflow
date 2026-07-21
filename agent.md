# AGENT.md

# TaskFlow — Project & Design Reference

> This document is the primary implementation guide for AI coding agents working on
> the TaskFlow project. Follow these conventions before making architectural,
> UI, or backend decisions.

---

# 1. Project Overview

TaskFlow is a Laravel + Inertia + Vue 3 SaaS task management application built as a take-home assignment.

## Tech Stack

- Laravel (latest stable)
- PHP 8.3+
- Vue 3
- Inertia.js
- Tailwind CSS
- MySQL / PostgreSQL
- Laravel Breeze (Vue + Inertia authentication)

---

# 2. Project Goals

The application should provide a clean modern task-management experience with:

- authentication
- dashboard
- task CRUD
- filtering
- sorting
- reusable UI components
- maintainable Laravel architecture
- clean Vue component composition

---

# 3. Current Progress

## Completed

- Laravel installed
- Breeze authentication
    - Register
    - Login
    - Logout
- Inertia configured
- Vue application bootstrapped
- Sidebar layout
- Topbar layout
- Responsive application shell
- Dashboard page scaffolded

## Remaining

- Task CRUD
- Dashboard widgets
- KPI cards
- Task table
- Filters
- Sorting
- Search
- Validation via Form Requests
- API Resources
- Database seeders
- Badge components
- Pagination

---

# 4. Application Architecture

Frontend

Laravel
    ↓
Inertia
    ↓
Vue Pages
    ↓
AppLayout
    ↓
Components

Backend

Routes
    ↓
Controllers
    ↓
Form Requests
    ↓
Models
    ↓
Resources
    ↓
Database

---

# 5. Folder Structure

resources/js/

```
Layouts/
    AppLayout.vue
    AuthenticatedLayout.vue

Components/
    Navigation/
        Sidebar.vue
        SidebarLink.vue
        SidebarProjectItem.vue
        Topbar.vue
        UserMenu.vue

    (Breeze Components)
        ApplicationLogo.vue
        Checkbox.vue
        DangerButton.vue
        Dropdown.vue
        DropdownLink.vue
        InputError.vue
        InputLabel.vue
        Modal.vue
        NavLink.vue
        PrimaryButton.vue
        ResponsiveNavLink.vue
        SecondaryButton.vue
        TextInput.vue

Pages/
    Dashboard.vue

    Tasks/
        Index.vue
        Create.vue
        Edit.vue
```

Backend

```
app/

Models/
Controllers/
Http/
    Requests/
    Resources/

database/

migrations/
seeders/

routes/
```

---

# 6. Layout Responsibilities

## AppLayout.vue

Responsible for:

- overall application shell
- sidebar
- topbar
- page content

Each page should only provide:

- title
- subtitle (optional)
- page content

---

## Topbar.vue

Responsibilities

- presentation only
- receives:

```
title
subtitle
```

Provides slot:

```
topbar-actions
```

Example usage:

```
New Task button
Export button
Filters
```

---

## Sidebar.vue

Responsibilities

- navigation items
- project list

Current implementation:

- arrays stored inline

Future implementation:

- backend-driven navigation
- permission-aware navigation
- composables

---

# 7. Routing Guidelines

Pages should map closely to resources.

Example:

```
Dashboard

GET /
```

Tasks

```
GET     /tasks
GET     /tasks/create
POST    /tasks
GET     /tasks/{task}/edit
PUT     /tasks/{task}
DELETE  /tasks/{task}
```

---

# 8. Backend Standards

Always use:

- Form Requests
- API Resources
- Route Model Binding
- Policies (when permissions exist)
- Validation classes

Avoid:

- validation inside controllers
- business logic inside controllers

Controllers should stay thin.

---

# 9. Frontend Standards

Use:

- Composition API
- `<script setup>`
- reusable components
- computed properties
- composables where appropriate

Avoid:

- duplicated markup
- duplicated badge styling
- inline business logic

---

# 10. Component Standards

Create reusable components whenever UI repeats.

Examples:

```
StatusBadge.vue

PriorityBadge.vue

KpiCard.vue

TaskTable.vue

EmptyState.vue

ConfirmDeleteModal.vue
```

---

# 11. Design Tokens

## Primary Color

```
#4F39F6
```

Usage

- active navigation
- primary buttons
- links
- highlights

---

## Slate Colors

Primary text

```
#0F172B
```

Alternative heading

```
#1D293D
```

Secondary text

```
#45556C
```

Muted text

```
#90A1B9
```

Borders

```
#E2E8F0
```

Background

```
#F8FAFC
```

---

## Semantic Colors

Success

Foreground

```
#007A55
```

Background

```
#ECFDF5
```

Danger

Foreground

```
#E7000B
```

Background

```
#FEF2F2
```

Warning

Foreground

```
#E17100
```

Background

```
#FFFBEB
```

Info

Foreground

```
#1447E6
```

Background

```
#EFF6FF
```

---

# 12. Border Radius

Default

```
rounded-2xl
```

Equivalent

```
16px
```

Use for:

- cards
- buttons
- modals
- navigation
- panels

Badges

```
rounded-full
```

---

# 13. Typography

Font

```
Inter
```

Weights

```
400
500
600
700
```

Page title

```
16px
Semibold
```

Card value

```
24px
Bold
```

Body

```
14px
Medium
```

Helper text

```
12px
Regular
```

---

# 14. Layout Dimensions

Sidebar

```
240px
```

Topbar

```
64px
```

Content padding

```
24px
```

Section gap

```
24px
```

---

# 15. Dashboard

Current status

Visual scaffold only.

Contains:

- page header
- placeholder KPI area
- placeholder table area

Future implementation

- KPI cards
- recent tasks
- upcoming deadlines
- activity summary

---

# 16. Tasks Module

Features

- Create task
- Edit task
- Delete task
- View task
- Pagination
- Search
- Sorting
- Filtering

Columns

```
ID

Task

Status

Priority

Assignee

Due Date
```

---

# 17. Status Values

Recommended

```
Todo

In Progress

Done
```

Badge colors

Todo

Gray

In Progress

Blue

Done

Green

---

# 18. Priority Values

Recommended

```
Low

Medium

High

Urgent
```

Suggested colors

Low

Gray

Medium

Yellow

High

Orange

Urgent

Red

---

# 19. Mobile Behavior

Desktop Figma only.

Implementation assumptions

Sidebar

```
fixed
240px
```

Below lg

```
off-canvas drawer
```

Topbar

```
always visible
```

---

# 20. Known Assumptions

Because the Figma was incomplete:

- My Tasks badge is hardcoded.
- Settings page is placeholder.
- Dashboard widgets intentionally empty.
- Mobile drawer implemented conventionally.
- Permissions not yet implemented.
- Navigation is static.

---

# 21. Database Direction

Future relationships

User

```
hasMany(Task)
```

Task

```
belongsTo(User)
```

Potential future models

```
Project

Comment

Activity

Label
```

---

# 22. Coding Standards

Laravel

- skinny controllers
- fat models/services where appropriate
- Form Requests
- Resources
- Eloquent relationships

Vue

- Composition API
- script setup
- reusable components
- props over duplication
- slots when flexibility is needed

Tailwind

- utility-first
- avoid custom CSS unless necessary

---

# 23. Naming Conventions

Vue

```
AppLayout.vue

Topbar.vue

Sidebar.vue

StatusBadge.vue

PriorityBadge.vue

KpiCard.vue
```

Pages

```
Dashboard.vue

Index.vue

Create.vue

Edit.vue
```

Controllers

```
TaskController
```

Requests

```
StoreTaskRequest

UpdateTaskRequest
```

Resources

```
TaskResource
```

---

# 24. Development Principles

Prefer

- reusable components
- consistency
- readability
- predictable architecture
- Laravel conventions
- Vue conventions

Avoid

- duplicated code
- inline styles
- magic values
- business logic inside views
- giant Vue components

---

# 25. Next Implementation Roadmap

## Phase 1

- Task migration
- Task model
- Controller
- Requests
- Resource

---

## Phase 2

Task CRUD

- Create
- Edit
- Delete
- Validation

---

## Phase 3

Task Index

- Table
- Filters
- Sorting
- Search
- Pagination

---

## Phase 4

Dashboard

- KPI cards
- Charts (optional)
- Recent tasks
- Statistics

---

## Phase 5

Reusable Components

- StatusBadge
- PriorityBadge
- EmptyState
- KpiCard
- ConfirmDeleteModal
- TaskTable

---

## Phase 6

Polish

- Loading states
- Skeletons
- Toast notifications
- Better transitions
- Accessibility improvements
- Dark mode (optional)

---

# 26. AI Agent Instructions

When modifying this project:

1. Preserve existing architecture.
2. Prefer reusable components.
3. Follow Laravel best practices.
4. Follow Vue Composition API.
5. Use Form Requests for validation.
6. Use API Resources for responses.
7. Match the design tokens.
8. Do not introduce unnecessary dependencies.
9. Keep controllers thin.
10. Keep components focused.
11. Maintain consistent spacing using Tailwind utilities.
12. Use rounded-2xl for cards unless another radius is explicitly required.
13. Reuse badge components instead of duplicating badge markup.
14. Follow the existing layout system using `AppLayout.vue`.
15. Keep code production-ready and readable.

---

# 27. Project Status Summary

Overall Progress

```
████████░░░░░░░░░░░░ 40%
```

Completed

- Authentication
- Application shell
- Responsive navigation
- Dashboard scaffold

In Progress

- Tasks module

Upcoming

- Dashboard metrics
- Task CRUD
- Resources
- Requests
- Reusable UI components
- Production polish

# Lucide Icons

Use **lucide-vue-next** exclusively for icons.

Import example:

```vue
import {
    LayoutDashboard,
    CheckSquare,
    FolderKanban,
    Settings,
    Plus,
    Search,
    Filter,
    ArrowUpDown,
    Calendar,
    Clock3,
    User,
    Users,
    Bell,
    ChevronDown,
    ChevronRight,
    MoreHorizontal,
    Pencil,
    Trash2,
    Eye,
    CircleCheck,
    CircleDashed,
    AlertTriangle,
    Flag,
    X,
    Menu,
    PanelLeft,
    PanelLeftClose,
} from "lucide-vue-next";
```

Preferred icon mapping

Dashboard

```
LayoutDashboard
```

Tasks

```
CheckSquare
```

Projects

```
FolderKanban
```

Settings

```
Settings
```

Profile

```
User
```

Users

```
Users
```

Notifications

```
Bell
```

New Task

```
Plus
```

Search

```
Search
```

Filter

```
Filter
```

Sort

```
ArrowUpDown
```

Due Date

```
Calendar
```

Status

```
CircleCheck
CircleDashed
```

Priority

```
Flag
AlertTriangle
```

Edit

```
Pencil
```

Delete

```
Trash2
```

View

```
Eye
```

More Menu

```
MoreHorizontal
```

Sidebar Toggle

```
PanelLeft
PanelLeftClose
```

Mobile Menu

```
Menu
```

Close Modal

```
X
```

Use **18px–20px** icons by default.

Navigation icons should use:

```
w-5 h-5
```

Buttons:

```
w-4 h-4
```

Cards:

```
w-6 h-6
```
