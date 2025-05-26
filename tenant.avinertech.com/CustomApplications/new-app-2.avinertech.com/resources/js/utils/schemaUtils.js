export const hasLengthOrPrecision = (type) => {
  return ['string', 'char', 'decimal', 'float', 'double'].includes(type);
};

export const getLengthOrPrecisionLabel = (type) => {
  switch (type) {
    case 'decimal':
    case 'float':
    case 'double':
      return 'Precision';
    default:
      return 'Length';
  }
};

export const getLengthOrPrecisionPlaceholder = (type) => {
  switch (type) {
    case 'decimal':
      return '10,2';
    case 'float':
    case 'double':
      return '8,2';
    case 'string':
      return '255';
    case 'char':
      return '1';
    default:
      return '';
  }
};

export const generateMigration = (model) => {
  const tableName = model.tableType === 'pivot' 
    ? model.name.toLowerCase().split('_').sort().join('_')
    : model.name.toLowerCase().replace(/([a-z])([A-Z])/g, '$1_$2').toLowerCase() + 's';

  const fields = model.fields.map(field => {
    let definition = `$table->${field.type}`;
    
    if (hasLengthOrPrecision(field.type) && field.length) {
      definition += `('${field.name}', ${field.length})`;
    } else {
      definition += `('${field.name}')`;
    }

    if (field.nullable) definition += '->nullable()';
    if (field.unique) definition += '->unique()';
    if (field.indexed) definition += '->index()';
    if (field.encrypted) definition += '->encrypted()';

    return definition + ';';
  });

  return {
    tableName,
    fields,
    timestamps: true,
    softDeletes: true
  };
};

export const generateModel = (model) => {
  return {
    name: model.name,
    table: model.tableType === 'pivot' 
      ? model.name.toLowerCase().split('_').sort().join('_')
      : model.name.toLowerCase().replace(/([a-z])([A-Z])/g, '$1_$2').toLowerCase() + 's',
    fillable: model.fields.filter(f => !f.encrypted).map(f => f.name),
    hidden: model.fields.filter(f => f.encrypted).map(f => f.name),
    casts: model.fields.reduce((acc, field) => {
      if (['json', 'jsonb'].includes(field.type)) acc[field.name] = 'array';
      if (['boolean'].includes(field.type)) acc[field.name] = 'boolean';
      if (['datetime', 'date', 'time', 'timestamp'].includes(field.type)) acc[field.name] = field.type;
      return acc;
    }, {}),
    attributes: model.fields.reduce((acc, field) => {
      if (field.encrypted) acc[field.name] = 'encrypted';
      return acc;
    }, {}),
    relationships: []
  };
};

export const generateFactory = (model) => {
  return {
    name: model.name,
    definition: model.fields.reduce((acc, field) => {
      switch (field.type) {
        case 'string':
        case 'char':
          acc[field.name] = `$faker->${field.unique ? 'unique()->' : ''}word`;
          break;
        case 'text':
        case 'longText':
          acc[field.name] = '$faker->paragraph';
          break;
        case 'integer':
        case 'bigInteger':
          acc[field.name] = '$faker->numberBetween(1, 1000)';
          break;
        case 'boolean':
          acc[field.name] = '$faker->boolean';
          break;
        case 'datetime':
        case 'timestamp':
          acc[field.name] = '$faker->dateTime';
          break;
        case 'date':
          acc[field.name] = '$faker->date';
          break;
        case 'time':
          acc[field.name] = '$faker->time';
          break;
        case 'json':
        case 'jsonb':
          acc[field.name] = '[]';
          break;
        default:
          acc[field.name] = 'null';
      }
      return acc;
    }, {})
  };
}; 