import axios from 'axios';

class SchemaService {
    static async getTenantConfig(host) {
        try {
            const response = await axios.get(`/api/configuration/database`);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to fetch tenant configuration');
        }
    }

    static async testConnection(config) {
        try {
            const response = await axios.post('/api/configuration/test-connection', config);
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to test database connection');
        }
    }

    static async storeSchema(schemas) {
    try {
            const response = await axios.post(`/api/schema/`, { schemas });
            return response.data;
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Failed to store schema');
        }
    }

    // Generate MySQL queries from schema
    generateMySQLQueries(schema) {
        const queries = [];
        
        // Create table query
        const createTableQuery = this.generateCreateTableQuery(schema);
        queries.push(createTableQuery);

        // Generate indexes
        const indexQueries = this.generateIndexQueries(schema);
        queries.push(...indexQueries);

        return queries;
    }

    generateCreateTableQuery(schema) {
        const fields = schema.fields.map(field => {
            let fieldDef = `\`${field.name}\` ${this.mapFieldTypeToMySQL(field.type)}`;
            
            // Add nullable
            fieldDef += field.nullable ? ' NULL' : ' NOT NULL';
            
            // Add unique constraint
            if (field.unique) {
                fieldDef += ' UNIQUE';
            }

            return fieldDef;
        });

        // Add timestamps if not present
        if (!schema.fields.find(f => f.name === 'created_at')) {
            fields.push('`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
        }
        if (!schema.fields.find(f => f.name === 'updated_at')) {
            fields.push('`updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        }

        return `CREATE TABLE IF NOT EXISTS \`${schema.name}\` (
            ${fields.join(',\n            ')}
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;`;
    }

    generateIndexQueries(schema) {
        const queries = [];
        const indexedFields = schema.fields.filter(f => f.indexed);

        if (indexedFields.length > 0) {
            const indexName = `idx_${schema.name}_${indexedFields.map(f => f.name).join('_')}`;
            const indexFields = indexedFields.map(f => `\`${f.name}\``).join(', ');
            queries.push(`CREATE INDEX \`${indexName}\` ON \`${schema.name}\` (${indexFields});`);
        }

        return queries;
    }

    mapFieldTypeToMySQL(type) {
        const typeMap = {
            // Basic Types
            string: 'VARCHAR(255)',
            integer: 'INT',
            float: 'FLOAT',
            boolean: 'BOOLEAN',
            datetime: 'DATETIME',
            date: 'DATE',
            time: 'TIME',
            timestamp: 'TIMESTAMP',
            
            // Text Types
            text: 'TEXT',
            longText: 'LONGTEXT',
            mediumText: 'MEDIUMTEXT',
            char: 'CHAR(255)',
            
            // Numeric Types
            decimal: 'DECIMAL(10,2)',
            double: 'DOUBLE',
            bigInteger: 'BIGINT',
            unsignedInteger: 'INT UNSIGNED',
            unsignedBigInteger: 'BIGINT UNSIGNED',
            
            // Special Types
            json: 'JSON',
            jsonb: 'JSON',
            binary: 'BLOB',
            uuid: 'CHAR(36)',
            ipAddress: 'VARCHAR(45)',
            macAddress: 'VARCHAR(17)'
        };

        return typeMap[type] || 'VARCHAR(255)';
    }
}

export default SchemaService; 