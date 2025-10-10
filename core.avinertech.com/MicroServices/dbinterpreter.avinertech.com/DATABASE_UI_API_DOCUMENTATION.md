# MySQL Database Management UI - Laravel API Integration Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [State Management Architecture](#state-management-architecture)
3. [UI Components & Strategies](#ui-components--strategies)
4. [API Contract Specifications](#api-contract-specifications)
5. [Missing Strategies (Gap Analysis)](#missing-strategies-gap-analysis)
6. [Button Actions & Strategies](#button-actions--strategies)
7. [Data Flow & Integration Points](#data-flow--integration-points)
8. [Security & Validation Rules](#security--validation-rules)
9. [Implementation Guidelines](#implementation-guidelines)

---

## System Overview

### Purpose
This documentation provides complete specifications for implementing Laravel APIs to support the MySQL Database Management UI. The UI is designed as a **secure, UI-only** MySQL GUI that allows users to manage and explore existing databases with **no ability to connect to new servers** or **drop databases/tables**, but **full power to create tables** with every MySQL datatype and option.

### Architecture Principles
- **Phase 1**: UI-only with state-based simulation
- **Phase 2**: Real API integration with maintained UI compatibility
- **Security First**: No dangerous operations (DROP TABLE/DATABASE) anywhere
- **Complete MySQL Support**: All datatypes, indexes, constraints, partitioning
- **Real-time SQL Preview**: Every action generates previewable SQL

---

## State Management Architecture

### Core State Structure

```typescript
interface AppState {
  connection: { name: string; readonly: true }; // Fixed connection
  schemas: Record<SchemaId, Schema>;
  tables: Record<TableId, Table>;
  views: Record<string, ViewDef>;
  routines: Record<string, RoutineDef>;
  triggers: Record<string, TriggerDef>;
  events: Record<string, EventDef>;
  selection: { schemaId?: string; tableId?: string; tab?: string };
  changes: ChangeSet[];
  undoStack: ChangeSet[];
  redoStack: ChangeSet[];
}
```

### Entity Types & Relationships

#### Schema Entity
```typescript
interface Schema {
  id: SchemaId;
  name: string;
  tables?: number;    // Count for UI display
  views?: number;     // Count for UI display
  routines?: number;  // Count for UI display
}
```

#### Table Entity (Complete Structure)
```typescript
interface Table {
  id: TableId;
  schemaId: SchemaId;
  name: string;
  columns: Column[];
  indexes: Index[];
  foreignKeys: ForeignKey[];
  checks: CheckConstraint[];
  options: TableOptions;
  partitions?: Partitioning;
}
```

#### Column Entity (All MySQL Features)
```typescript
interface Column {
  id: ColumnId;
  name: string;
  dataType: MySqlDataType;
  nullable: boolean;
  default?: DefaultSpec;
  autoIncrement?: boolean;
  generated?: GeneratedSpec;  // VIRTUAL/STORED
  columnComment?: string;
  charset?: string;
  collation?: string;
  srid?: number;  // Spatial
  unsigned?: boolean;
  zerofill?: boolean;
  length?: number;      // VARCHAR, CHAR, etc.
  precision?: number;  // DECIMAL
  scale?: number;      // DECIMAL
  enumValues?: string[]; // ENUM/SET
}
```

#### Index Entity
```typescript
interface Index {
  id: IndexId;
  name: string;
  type: 'BTREE' | 'HASH' | 'FULLTEXT' | 'SPATIAL';
  unique?: boolean;
  columns: { columnId: ColumnId; length?: number; order?: 'ASC'|'DESC' }[];
  indexComment?: string;
  parser?: string;  // Fulltext parser
  visible?: boolean; // MySQL 8.0+
}
```

#### Foreign Key Entity
```typescript
interface ForeignKey {
  id: ConstraintId;
  name: string;
  columns: ColumnId[];
  refSchemaId: SchemaId;
  refTableId: TableId;
  refColumns: string[];
  onDelete?: 'RESTRICT'|'CASCADE'|'SET NULL'|'NO ACTION';
  onUpdate?: 'RESTRICT'|'CASCADE'|'SET NULL'|'NO ACTION';
  match?: 'FULL'|'PARTIAL'|'SIMPLE';
}
```

#### Check Constraint Entity
```typescript
interface CheckConstraint {
  id: ConstraintId;
  name: string;
  expression: string;
  enforced?: boolean; // MySQL 8.0+
}
```

#### Partitioning Entity
```typescript
interface Partitioning {
  type: 'RANGE'|'LIST'|'HASH'|'KEY';
  expression?: string;
  columns?: string[];
  partitions?: number;
  subpartition?: { 
    type: 'HASH'|'KEY'; 
    expression?: string; 
    columns?: string[]; 
    partitions?: number; 
  };
  specs?: PartitionSpec[];
}
```

#### Table Options Entity
```typescript
interface TableOptions {
  engine?: 'InnoDB'|'MyISAM'|'MEMORY'|'CSV'|'ARCHIVE'|'NDB'|'MyRocks'|'Other';
  autoIncrement?: number;
  charset?: string;
  collation?: string;
  rowFormat?: 'DEFAULT'|'COMPACT'|'REDUNDANT'|'DYNAMIC'|'COMPRESSED';
  statsPersistent?: boolean;
  comment?: string;
  encryption?: 'Y'|'N';
}
```

### MySQL Data Types (Complete Coverage)

#### Numeric Types
- `BIT`, `TINYINT`, `SMALLINT`, `MEDIUMINT`, `INT`, `INTEGER`, `BIGINT`
- `DECIMAL(p,s)`, `NUMERIC(p,s)`, `FLOAT`, `DOUBLE`
- Attributes: `UNSIGNED`, `ZEROFILL`

#### Date/Time Types
- `DATE`, `TIME(fsp)`, `DATETIME(fsp)`, `TIMESTAMP(fsp)`, `YEAR`
- Defaults & `ON UPDATE CURRENT_TIMESTAMP` options

#### Character & Binary Types
- `CHAR(n)`, `VARCHAR(n)`, `BINARY(n)`, `VARBINARY(n)`
- `TINYBLOB`, `BLOB`, `MEDIUMBLOB`, `LONGBLOB`
- `TINYTEXT`, `TEXT`, `MEDIUMTEXT`, `LONGTEXT`
- Per-column charset & collation support

#### JSON Type
- `JSON` with generated column helpers for indexing

#### Enumeration Types
- `ENUM('...','...')`, `SET('...','...')` with value management

#### Spatial Types (InnoDB)
- `GEOMETRY`, `POINT`, `LINESTRING`, `POLYGON`
- `MULTIPOINT`, `MULTILINESTRING`, `MULTIPOLYGON`, `GEOMETRYCOLLECTION`
- SRID support, SPATIAL index only on geometry columns

---

## UI Components & Strategies

### 1. DatabasePage Component
**Location**: `src/pages/DatabasePage.tsx`
**Purpose**: Main database management interface

#### Tabs & Navigation
- **Overview Tab**: Schema summary cards, recent activity
- **Tables Tab**: Table listing with search/filter
- **Design Tab**: Table designer interface
- **Data Tab**: Data browsing and editing
- **Views Tab**: View management
- **Changes Tab**: Pending changes tracking

#### Header Actions
- **Refresh Button**: Reload schema/table data
- **Export SQL Button**: Export all pending changes

### 2. DatabaseSidebar Component
**Location**: `src/components/DatabaseSidebar.tsx`
**Purpose**: Hierarchical database explorer

#### Schema Navigation
- Schema selection and expansion
- Table/View/Routine/Trigger/Event counts
- Collapsible sections with lazy loading

### 3. TableDesigner Component
**Location**: `src/components/TableDesigner.tsx`
**Purpose**: Comprehensive table creation/editing

#### Design Tabs
- **Columns Tab**: Column management with all MySQL datatypes
- **Indexes Tab**: Index creation and management
- **Foreign Keys Tab**: Relationship management
- **Checks Tab**: Check constraint management
- **Partitions Tab**: Partitioning configuration
- **Options Tab**: Table-level options
- **SQL Preview Tab**: Real-time SQL generation

### 4. ColumnDesigner Component
**Location**: `src/components/ColumnDesigner.tsx`
**Purpose**: Advanced column management

#### Features
- Complete MySQL datatype support
- Column attributes (NULL, DEFAULT, AUTO_INCREMENT, GENERATED)
- Charset/collation per column
- Spatial SRID support
- Enum/SET value management
- Column reordering and duplication

### 5. DataBrowser Component
**Location**: `src/components/DataBrowser.tsx`
**Purpose**: Data viewing and editing

#### Features
- Paginated data display
- Search and filtering
- Row editing capabilities
- CSV/JSON export
- Add/Edit/Delete rows

### 6. SQLPreview Component
**Location**: `src/components/SQLPreview.tsx`
**Purpose**: Real-time SQL generation

#### Features
- Live SQL preview for all operations
- Copy to clipboard
- Export SQL files
- Syntax validation
- Development tips

### 7. PendingChanges Component
**Location**: `src/components/PendingChanges.tsx`
**Purpose**: Change tracking and audit

#### Features
- Change history tracking
- Undo/Redo functionality
- SQL export for all changes
- Change status management

---

## API Contract Specifications

### Base URL Structure
```
/api/database/
```

### Authentication
- Bearer token authentication required
- All endpoints require valid JWT token
- No connection management endpoints (security constraint)

### 1. Schema Management APIs

#### GET /api/database/schemas
**Purpose**: List all available schemas
**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": "schema1",
      "name": "ecommerce",
      "tables": 12,
      "views": 3,
      "routines": 5,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### 2. Table Management APIs

#### GET /api/database/schemas/{schemaId}/tables
**Purpose**: List tables in schema
**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": "table1",
      "name": "users",
      "engine": "InnoDB",
      "collation": "utf8mb4_0900_ai_ci",
      "rows": 15420,
      "comment": "User accounts and profiles",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

#### GET /api/database/schemas/{schemaId}/tables/{tableId}
**Purpose**: Get complete table structure
**Response**:
```json
{
  "success": true,
  "data": {
    "id": "table1",
    "schemaId": "schema1",
    "name": "users",
    "columns": [...],
    "indexes": [...],
    "foreignKeys": [...],
    "checks": [...],
    "options": {...},
    "partitions": {...}
  }
}
```

#### POST /api/database/schemas/{schemaId}/tables
**Purpose**: Create new table
**Request Body**: Complete Table object
**Response**:
```json
{
  "success": true,
  "data": {
    "id": "new_table_id",
    "sql": "CREATE TABLE `users` (...)"
  }
}
```

#### PATCH /api/database/schemas/{schemaId}/tables/{tableId}
**Purpose**: Modify existing table
**Request Body**: ChangeSet object
**Response**:
```json
{
  "success": true,
  "data": {
    "sql": "ALTER TABLE `users` ADD COLUMN `email` VARCHAR(255)"
  }
}
```

### 3. Column Management APIs

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/columns
**Purpose**: Add column to table
**Request Body**: Column object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/tables/{tableId}/columns/{columnId}
**Purpose**: Modify column
**Request Body**: Column updates
**Response**: Generated SQL

#### DELETE /api/database/schemas/{schemaId}/tables/{tableId}/columns/{columnId}
**Purpose**: Remove column
**Response**: Generated SQL

### 4. Index Management APIs

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/indexes
**Purpose**: Add index
**Request Body**: Index object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/tables/{tableId}/indexes/{indexId}
**Purpose**: Modify index
**Request Body**: Index updates
**Response**: Generated SQL

#### DELETE /api/database/schemas/{schemaId}/tables/{tableId}/indexes/{indexId}
**Purpose**: Remove index
**Response**: Generated SQL

### 5. Foreign Key Management APIs

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/foreign-keys
**Purpose**: Add foreign key
**Request Body**: ForeignKey object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/tables/{tableId}/foreign-keys/{constraintId}
**Purpose**: Modify foreign key
**Request Body**: ForeignKey updates
**Response**: Generated SQL

#### DELETE /api/database/schemas/{schemaId}/tables/{tableId}/foreign-keys/{constraintId}
**Purpose**: Remove foreign key
**Response**: Generated SQL

### 6. Check Constraint APIs

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/checks
**Purpose**: Add check constraint
**Request Body**: CheckConstraint object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/tables/{tableId}/checks/{constraintId}
**Purpose**: Modify check constraint
**Request Body**: CheckConstraint updates
**Response**: Generated SQL

#### DELETE /api/database/schemas/{schemaId}/tables/{tableId}/checks/{constraintId}
**Purpose**: Remove check constraint
**Response**: Generated SQL

### 7. Partitioning APIs

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions
**Purpose**: Enable partitioning
**Request Body**: Partitioning object
**Response**: Generated SQL

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions/add
**Purpose**: Add partition
**Request Body**: PartitionSpec object
**Response**: Generated SQL

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions/reorganize
**Purpose**: Reorganize partitions
**Request Body**: Partition reorganization data
**Response**: Generated SQL

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions/truncate
**Purpose**: Truncate partition
**Request Body**: Partition ID
**Response**: Generated SQL

### 8. Data Management APIs

#### GET /api/database/schemas/{schemaId}/tables/{tableId}/data
**Purpose**: Get table data
**Query Parameters**: page, limit, search, filters
**Response**:
```json
{
  "success": true,
  "data": {
    "rows": [...],
    "total": 15420,
    "page": 1,
    "per_page": 50
  }
}
```

#### POST /api/database/schemas/{schemaId}/tables/{tableId}/data
**Purpose**: Insert row
**Request Body**: Row data
**Response**: Inserted row with ID

#### PATCH /api/database/schemas/{schemaId}/tables/{tableId}/data/{rowId}
**Purpose**: Update row
**Request Body**: Row updates
**Response**: Updated row

#### DELETE /api/database/schemas/{schemaId}/tables/{tableId}/data/{rowId}
**Purpose**: Delete row
**Response**: Success confirmation

### 9. View Management APIs

#### GET /api/database/schemas/{schemaId}/views
**Purpose**: List views
**Response**: Array of ViewDef objects

#### POST /api/database/schemas/{schemaId}/views
**Purpose**: Create view
**Request Body**: ViewDef object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/views/{viewId}
**Purpose**: Modify view
**Request Body**: ViewDef updates
**Response**: Generated SQL

### 10. Routine Management APIs

#### GET /api/database/schemas/{schemaId}/routines
**Purpose**: List procedures and functions
**Response**: Array of RoutineDef objects

#### POST /api/database/schemas/{schemaId}/routines
**Purpose**: Create routine
**Request Body**: RoutineDef object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/routines/{routineId}
**Purpose**: Modify routine
**Request Body**: RoutineDef updates
**Response**: Generated SQL

### 11. Trigger Management APIs

#### GET /api/database/schemas/{schemaId}/triggers
**Purpose**: List triggers
**Response**: Array of TriggerDef objects

#### POST /api/database/schemas/{schemaId}/triggers
**Purpose**: Create trigger
**Request Body**: TriggerDef object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/triggers/{triggerId}
**Purpose**: Modify trigger
**Request Body**: TriggerDef updates
**Response**: Generated SQL

### 12. Event Management APIs

#### GET /api/database/schemas/{schemaId}/events
**Purpose**: List events
**Response**: Array of EventDef objects

#### POST /api/database/schemas/{schemaId}/events
**Purpose**: Create event
**Request Body**: EventDef object
**Response**: Generated SQL

#### PATCH /api/database/schemas/{schemaId}/events/{eventId}
**Purpose**: Modify event
**Request Body**: EventDef updates
**Response**: Generated SQL

### 13. SQL Preview API

#### POST /api/database/preview-sql
**Purpose**: Generate SQL preview for any operation
**Request Body**: ChangeSet object
**Response**:
```json
{
  "success": true,
  "data": {
    "sql": "CREATE TABLE `users` (...)",
    "valid": true,
    "errors": []
  }
}
```

---

## Missing Strategies (Gap Analysis)

### 1. IndexDesigner Component - MISSING STRATEGY
**Current Status**: Placeholder with "coming soon" message
**Required Implementation**:
- Index type selection (BTREE, HASH, FULLTEXT, SPATIAL)
- Column selection with order (ASC/DESC)
- Index prefix length configuration
- Unique constraint toggle
- Index visibility (MySQL 8.0+)
- Fulltext parser selection
- Index comment field
- Auto-name generation for indexes

**API Endpoints Needed**:
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/indexes`
- `PATCH /api/database/schemas/{schemaId}/tables/{tableId}/indexes/{indexId}`
- `DELETE /api/database/schemas/{schemaId}/tables/{tableId}/indexes/{indexId}`

### 2. ForeignKeyDesigner Component - MISSING STRATEGY
**Current Status**: Placeholder with "coming soon" message
**Required Implementation**:
- Source table column selection
- Target schema/table selection
- Target column mapping
- ON DELETE action selection (RESTRICT, CASCADE, SET NULL, NO ACTION)
- ON UPDATE action selection
- Match type selection (FULL, PARTIAL, SIMPLE)
- Type compatibility validation
- Auto-name generation for foreign keys

**API Endpoints Needed**:
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/foreign-keys`
- `PATCH /api/database/schemas/{schemaId}/tables/{tableId}/foreign-keys/{constraintId}`
- `DELETE /api/database/schemas/{schemaId}/tables/{tableId}/foreign-keys/{constraintId}`

### 3. CheckConstraintDesigner Component - MISSING STRATEGY
**Current Status**: Placeholder with "coming soon" message
**Required Implementation**:
- Expression editor with syntax highlighting
- Expression validation
- ENFORCED toggle (MySQL 8.0+)
- Constraint name field
- Auto-name generation for check constraints

**API Endpoints Needed**:
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/checks`
- `PATCH /api/database/schemas/{schemaId}/tables/{tableId}/checks/{constraintId}`
- `DELETE /api/database/schemas/{schemaId}/tables/{tableId}/checks/{constraintId}`

### 4. PartitionDesigner Component - MISSING STRATEGY
**Current Status**: Placeholder with "coming soon" message
**Required Implementation**:
- Partition type selection (RANGE, LIST, HASH, KEY)
- Expression editor for RANGE/LIST/HASH
- Column selection for KEY partitioning
- Partition count for HASH/KEY
- Subpartitioning configuration
- Partition specifications for RANGE/LIST
- Partition maintenance operations (ADD, REORGANIZE, TRUNCATE)
- Partition templates and presets

**API Endpoints Needed**:
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions`
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions/add`
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions/reorganize`
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/partitions/truncate`

### 5. View Management - MISSING STRATEGY
**Current Status**: Basic placeholder in Views tab
**Required Implementation**:
- View creation wizard
- SQL editor with syntax highlighting
- Algorithm selection (UNDEFINED, MERGE, TEMPTABLE)
- Security context selection (DEFINER, INVOKER)
- Check option selection (NONE, LOCAL, CASCADED)
- View dependency analysis
- View validation

**API Endpoints Needed**:
- `GET /api/database/schemas/{schemaId}/views`
- `POST /api/database/schemas/{schemaId}/views`
- `PATCH /api/database/schemas/{schemaId}/views/{viewId}`
- `DELETE /api/database/schemas/{schemaId}/views/{viewId}`

### 6. Routine Management - MISSING STRATEGY
**Current Status**: Listed in sidebar but no management interface
**Required Implementation**:
- Procedure/Function creation wizard
- Parameter management (IN, OUT, INOUT)
- Return type specification for functions
- Deterministic flag
- SQL security context
- Body editor with syntax highlighting
- Parameter validation

**API Endpoints Needed**:
- `GET /api/database/schemas/{schemaId}/routines`
- `POST /api/database/schemas/{schemaId}/routines`
- `PATCH /api/database/schemas/{schemaId}/routines/{routineId}`
- `DELETE /api/database/schemas/{schemaId}/routines/{routineId}`

### 7. Trigger Management - MISSING STRATEGY
**Current Status**: Listed in sidebar but no management interface
**Required Implementation**:
- Trigger creation wizard
- Timing selection (BEFORE, AFTER)
- Event selection (INSERT, UPDATE, DELETE)
- Table selection
- Body editor with syntax highlighting
- Trigger validation

**API Endpoints Needed**:
- `GET /api/database/schemas/{schemaId}/triggers`
- `POST /api/database/schemas/{schemaId}/triggers`
- `PATCH /api/database/schemas/{schemaId}/triggers/{triggerId}`
- `DELETE /api/database/schemas/{schemaId}/triggers/{triggerId}`

### 8. Event Management - MISSING STRATEGY
**Current Status**: Listed in sidebar but no management interface
**Required Implementation**:
- Event creation wizard
- Schedule configuration (EVERY n UNIT, AT TIMESTAMP)
- Enable/disable toggle
- On completion behavior (DROP, PRESERVE)
- Time zone selection
- Body editor with syntax highlighting

**API Endpoints Needed**:
- `GET /api/database/schemas/{schemaId}/events`
- `POST /api/database/schemas/{schemaId}/events`
- `PATCH /api/database/schemas/{schemaId}/events/{eventId}`
- `DELETE /api/database/schemas/{schemaId}/events/{eventId}`

### 9. Advanced Data Operations - MISSING STRATEGY
**Current Status**: Basic data browsing only
**Required Implementation**:
- Bulk data operations (bulk insert, update, delete)
- Data import (CSV, JSON, Excel)
- Data export (CSV, JSON, Excel, SQL)
- Data validation and constraints
- Transaction management
- Data backup and restore

**API Endpoints Needed**:
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/data/bulk`
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/data/import`
- `GET /api/database/schemas/{schemaId}/tables/{tableId}/data/export`
- `POST /api/database/schemas/{schemaId}/tables/{tableId}/data/validate`

### 10. Schema Analysis & Optimization - MISSING STRATEGY
**Current Status**: No analysis features
**Required Implementation**:
- Table size analysis
- Index usage statistics
- Query performance analysis
- Schema optimization suggestions
- Dependency analysis
- Schema comparison tools

**API Endpoints Needed**:
- `GET /api/database/schemas/{schemaId}/analysis/size`
- `GET /api/database/schemas/{schemaId}/analysis/performance`
- `GET /api/database/schemas/{schemaId}/analysis/optimization`
- `GET /api/database/schemas/{schemaId}/analysis/dependencies`

---

## Button Actions & Strategies

### DatabasePage Header Actions

#### 1. Refresh Button - STRATEGY: SCHEMA_REFRESH
**Location**: DatabasePage header
**Action**: Reload all schema and table data
**API Call**: `GET /api/database/schemas`
**State Update**: Refresh schemas, tables, and metadata
**UI Feedback**: Loading spinner, success toast

#### 2. Export SQL Button - STRATEGY: EXPORT_ALL_SQL
**Location**: DatabasePage header
**Action**: Export all pending changes as SQL file
**API Call**: `POST /api/database/export-sql`
**State Update**: None (download only)
**UI Feedback**: Download dialog, success toast

### DatabaseSidebar Actions

#### 3. Schema Selection - STRATEGY: SCHEMA_SELECT
**Location**: DatabaseSidebar
**Action**: Select schema and load its contents
**API Call**: `GET /api/database/schemas/{schemaId}/tables`
**State Update**: Update selectedSchema, load tables
**UI Feedback**: Highlight selected schema, load tables

#### 4. Table Selection - STRATEGY: TABLE_SELECT
**Location**: DatabaseSidebar
**Action**: Select table and load its structure
**API Call**: `GET /api/database/schemas/{schemaId}/tables/{tableId}`
**State Update**: Update selectedTable, load table structure
**UI Feedback**: Highlight selected table, switch to design tab

#### 5. Create Table Button - STRATEGY: CREATE_TABLE_INIT
**Location**: DatabaseSidebar
**Action**: Initialize table creation process
**API Call**: None (UI state only)
**State Update**: Set isCreatingTable = true, switch to design tab
**UI Feedback**: Switch to TableDesigner in create mode

### TableDesigner Actions

#### 6. Save Table Button - STRATEGY: SAVE_TABLE
**Location**: TableDesigner header
**Action**: Save table structure (create or update)
**API Call**: `POST /api/database/schemas/{schemaId}/tables` or `PATCH /api/database/schemas/{schemaId}/tables/{tableId}`
**State Update**: Add/update table in state, add to changes
**UI Feedback**: Success toast, close designer

#### 7. Cancel Button - STRATEGY: CANCEL_TABLE_EDIT
**Location**: TableDesigner header
**Action**: Cancel table editing without saving
**API Call**: None
**State Update**: Reset table state, close designer
**UI Feedback**: Close designer, show confirmation if unsaved changes

### ColumnDesigner Actions

#### 8. Add Column Button - STRATEGY: ADD_COLUMN
**Location**: ColumnDesigner
**Action**: Add new column to table
**API Call**: `POST /api/database/schemas/{schemaId}/tables/{tableId}/columns`
**State Update**: Add column to table.columns, add to changes
**UI Feedback**: Add column to list, show column editor

#### 9. Edit Column Button - STRATEGY: EDIT_COLUMN
**Location**: ColumnDesigner table row
**Action**: Edit existing column
**API Call**: None (UI state only)
**State Update**: Set editingColumn, open column editor
**UI Feedback**: Open column editor dialog

#### 10. Delete Column Button - STRATEGY: DELETE_COLUMN
**Location**: ColumnDesigner table row
**Action**: Remove column from table
**API Call**: `DELETE /api/database/schemas/{schemaId}/tables/{tableId}/columns/{columnId}`
**State Update**: Remove column from table.columns, add to changes
**UI Feedback**: Remove column from list, confirmation dialog

#### 11. Duplicate Column Button - STRATEGY: DUPLICATE_COLUMN
**Location**: ColumnDesigner table row
**Action**: Create copy of column
**API Call**: None (UI state only)
**State Update**: Create new column with copied properties
**UI Feedback**: Add new column to list, open editor

#### 12. Move Column Up Button - STRATEGY: MOVE_COLUMN_UP
**Location**: ColumnDesigner table row
**Action**: Move column up in order
**API Call**: `PATCH /api/database/schemas/{schemaId}/tables/{tableId}/columns/{columnId}/move`
**State Update**: Reorder columns array
**UI Feedback**: Update column order in UI

#### 13. Move Column Down Button - STRATEGY: MOVE_COLUMN_DOWN
**Location**: ColumnDesigner table row
**Action**: Move column down in order
**API Call**: `PATCH /api/database/schemas/{schemaId}/tables/{tableId}/columns/{columnId}/move`
**State Update**: Reorder columns array
**UI Feedback**: Update column order in UI

### DataBrowser Actions

#### 14. Search Data Button - STRATEGY: SEARCH_DATA
**Location**: DataBrowser header
**Action**: Search within table data
**API Call**: `GET /api/database/schemas/{schemaId}/tables/{tableId}/data?search={query}`
**State Update**: Update data rows with search results
**UI Feedback**: Show filtered results, update pagination

#### 15. Filter Data Button - STRATEGY: FILTER_DATA
**Location**: DataBrowser header
**Action**: Apply filters to table data
**API Call**: `GET /api/database/schemas/{schemaId}/tables/{tableId}/data?filters={filters}`
**State Update**: Update data rows with filtered results
**UI Feedback**: Show filtered results, update pagination

#### 16. Add Row Button - STRATEGY: ADD_ROW
**Location**: DataBrowser header
**Action**: Add new row to table
**API Call**: `POST /api/database/schemas/{schemaId}/tables/{tableId}/data`
**State Update**: Add row to data, update total count
**UI Feedback**: Add row to table, show success message

#### 17. Export Data Button - STRATEGY: EXPORT_DATA
**Location**: DataBrowser header
**Action**: Export table data
**API Call**: `GET /api/database/schemas/{schemaId}/tables/{tableId}/data/export`
**State Update**: None (download only)
**UI Feedback**: Download dialog, success toast

#### 18. Edit Row Button - STRATEGY: EDIT_ROW
**Location**: DataBrowser table row
**Action**: Edit existing row
**API Call**: `PATCH /api/database/schemas/{schemaId}/tables/{tableId}/data/{rowId}`
**State Update**: Update row in data
**UI Feedback**: Update row in table, show success message

#### 19. View Row Button - STRATEGY: VIEW_ROW
**Location**: DataBrowser table row
**Action**: View row details
**API Call**: None (UI state only)
**State Update**: Set viewing row
**UI Feedback**: Open row details dialog

### SQLPreview Actions

#### 20. Copy SQL Button - STRATEGY: COPY_SQL
**Location**: SQLPreview component
**Action**: Copy SQL to clipboard
**API Call**: None
**State Update**: None
**UI Feedback**: Success toast, button state change

#### 21. Export SQL Button - STRATEGY: EXPORT_SQL
**Location**: SQLPreview component
**Action**: Export SQL to file
**API Call**: None
**State Update**: None
**UI Feedback**: Download dialog, success toast

### PendingChanges Actions

#### 22. Undo Button - STRATEGY: UNDO_CHANGE
**Location**: PendingChanges header
**Action**: Undo last change
**API Call**: `POST /api/database/undo`
**State Update**: Move change from changes to redoStack
**UI Feedback**: Update changes list, success toast

#### 23. Redo Button - STRATEGY: REDO_CHANGE
**Location**: PendingChanges header
**Action**: Redo last undone change
**API Call**: `POST /api/database/redo`
**State Update**: Move change from redoStack to changes
**UI Feedback**: Update changes list, success toast

#### 24. Export All SQL Button - STRATEGY: EXPORT_ALL_CHANGES
**Location**: PendingChanges header
**Action**: Export all pending changes as SQL
**API Call**: `POST /api/database/export-all-sql`
**State Update**: None (download only)
**UI Feedback**: Download dialog, success toast

#### 25. View Change Button - STRATEGY: VIEW_CHANGE
**Location**: PendingChanges table row
**Action**: View change details
**API Call**: None (UI state only)
**State Update**: Set viewing change
**UI Feedback**: Open change details dialog

#### 26. Delete Change Button - STRATEGY: DELETE_CHANGE
**Location**: PendingChanges table row
**Action**: Remove change from pending list
**API Call**: `DELETE /api/database/changes/{changeId}`
**State Update**: Remove change from changes array
**UI Feedback**: Remove change from list, success toast

---

## Data Flow & Integration Points

### 1. Initial Load Flow
```
User Access /database → Check Authentication → Load Schemas → Select Schema → Load Tables → Select Table → Load Structure
```

### 2. Table Creation Flow
```
Create Table Button → TableDesigner → Configure Columns → Configure Indexes → Configure Foreign Keys → Configure Checks → Configure Partitions → Configure Options → Generate SQL Preview → Save Table → API Call → Update State → Show Success
```

### 3. Column Management Flow
```
Add Column → Column Editor → Configure Properties → Validate → Save Column → API Call → Update Table State → Refresh UI
```

### 4. Data Operations Flow
```
Select Table → Load Data → Search/Filter → Edit Row → Validate → Save Changes → API Call → Update Data → Refresh UI
```

### 5. Change Tracking Flow
```
Any Modification → Create ChangeSet → Add to Changes Array → Generate SQL Preview → Store in Undo Stack → Update UI
```

---

## Security & Validation Rules

### 1. Authentication & Authorization
- Bearer token required for all API calls
- No connection management endpoints (security constraint)
- Role-based access control (Viewer, Designer roles)
- Session management with automatic logout

### 2. Input Validation
- MySQL identifier validation (length ≤ 64, backtick escape)
- Datatype-specific validations:
  - DECIMAL(p,s): 1 ≤ p ≤ 65; 0 ≤ s ≤ p
  - VARCHAR(n): 1 ≤ n ≤ 65535
  - ENUM/SET: non-empty list; individual values length ≤ 255 bytes
  - JSON: valid JSON syntax for defaults
  - Generated columns: expression validation
  - Spatial: SRID numeric validation

### 3. SQL Injection Prevention
- Parameterized queries for all database operations
- Input sanitization and validation
- SQL preview generation with proper escaping
- No direct SQL execution from UI

### 4. Dangerous Operations Prevention
- **BLOCKED**: DROP DATABASE, DROP TABLE operations
- **BLOCKED**: Connection management
- **ALLOWED**: All other MySQL operations with proper validation

---

## Implementation Guidelines

### 1. Laravel API Structure
```
app/
├── Http/Controllers/Api/
│   ├── DatabaseController.php
│   ├── SchemaController.php
│   ├── TableController.php
│   ├── ColumnController.php
│   ├── IndexController.php
│   ├── ForeignKeyController.php
│   ├── CheckConstraintController.php
│   ├── PartitionController.php
│   ├── DataController.php
│   ├── ViewController.php
│   ├── RoutineController.php
│   ├── TriggerController.php
│   └── EventController.php
├── Services/
│   ├── DatabaseService.php
│   ├── SqlGeneratorService.php
│   ├── ValidationService.php
│   └── SecurityService.php
├── Models/
│   ├── Schema.php
│   ├── Table.php
│   ├── Column.php
│   ├── Index.php
│   ├── ForeignKey.php
│   ├── CheckConstraint.php
│   ├── Partition.php
│   └── ChangeSet.php
└── Requests/
    ├── CreateTableRequest.php
    ├── UpdateTableRequest.php
    ├── CreateColumnRequest.php
    └── ...
```

### 2. Database Schema
```sql
-- Change tracking table
CREATE TABLE change_sets (
    id VARCHAR(36) PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    schema_id VARCHAR(64),
    table_id VARCHAR(64),
    action VARCHAR(50),
    payload JSON,
    sql_preview TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- User sessions for audit
CREATE TABLE user_sessions (
    id VARCHAR(36) PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    schema_id VARCHAR(64),
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 3. API Response Standards
```json
{
  "success": true,
  "data": {...},
  "message": "Operation completed successfully",
  "timestamp": "2024-01-15T10:30:00Z",
  "request_id": "uuid"
}
```

### 4. Error Handling
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid column definition",
    "details": {
      "field": "dataType",
      "value": "INVALID_TYPE",
      "constraint": "Must be valid MySQL datatype"
    }
  },
  "timestamp": "2024-01-15T10:30:00Z",
  "request_id": "uuid"
}
```

### 5. Rate Limiting & Performance
- API rate limiting: 100 requests/minute per user
- Database connection pooling
- Query optimization and indexing
- Caching for frequently accessed schemas/tables
- Pagination for large datasets

### 6. Logging & Monitoring
- All API calls logged with user ID and timestamp
- SQL generation logged for audit
- Performance metrics collection
- Error tracking and alerting
- User activity monitoring

---

## Conclusion

This documentation provides complete specifications for implementing Laravel APIs to support the MySQL Database Management UI. The system is designed with security-first principles, comprehensive MySQL feature support, and clear separation between UI and backend concerns.

**Key Implementation Priorities:**
1. **Complete the missing strategies** identified in the gap analysis
2. **Implement all button actions** with proper API endpoints
3. **Ensure security constraints** are enforced at the API level
4. **Maintain state consistency** between UI and backend
5. **Provide comprehensive validation** for all MySQL operations

**Review Points:**
- All API endpoints are clearly defined with request/response formats
- Security constraints are explicitly documented
- Missing functionality is clearly identified and prioritized
- Button actions are mapped to specific strategies and API calls
- State management architecture supports the UI requirements

This documentation should be reviewed by database experts and Laravel developers to ensure completeness and accuracy before implementation begins.
