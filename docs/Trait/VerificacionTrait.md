**Documentación del Trait VerificacionTrait**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Métodos](#métodos)
4. [Consideraciones de Uso](#consideraciones-de-uso)
5. [Ejemplos de Uso](#ejemplos-de-uso)

### Descripción General
El trait `VerificacionTrait` se utiliza para verificar y actualizar los campos de un modelo de Eloquent en Laravel. Proporciona funcionalidades para verificar si los valores han cambiado, validar la unicidad de los campos, crear registros nuevos y manejar tanto inserciones individuales como múltiples. Es útil para asegurar la integridad de los datos y evitar duplicaciones, especialmente cuando se trata de datos que deben ser únicos.

### Dependencias
Este trait depende de los siguientes componentes y servicios:

- **Modelos Eloquent**: El trait está diseñado para trabajar con instancias de modelos de Eloquent.
- **StringNormalizer**: Utilizado para normalizar cadenas, removiendo acentos y caracteres especiales para facilitar la comparación.
- **Facades**:
  - `Illuminate\Support\Facades\DB`: Utilizado para manejar transacciones en la base de datos.

### Métodos

#### verificar($modelo, array $campos, array $valoresActualizados)
Verifica los campos especificados en el modelo y los actualiza si se detectan cambios. Este método también se encarga de verificar la unicidad de los valores antes de guardar el modelo.

- **Parámetros**:
  - `$modelo`: Instancia del modelo a actualizar.
  - `$campos`: Array con los nombres de los campos a verificar.
  - `$valoresActualizados`: Array con los nuevos valores a actualizar en el modelo.
- **Lógica**: Revisa si el valor del campo ha cambiado y si es único antes de proceder con la actualización. Si se detecta un conflicto de unicidad, emite un mensaje de advertencia (`dispatch('warning')`) y no realiza el guardado.

#### create($modelo, array $campos, array $valoresNuevos)
Crea uno o varios nuevos registros en el modelo. Este método utiliza transacciones para asegurar la integridad de las inserciones, especialmente en caso de inserciones múltiples.

- **Parámetros**:
  - `$modelo`: Modelo en el que se creará el nuevo registro.
  - `$campos`: Array con los nombres de los campos a insertar.
  - `$valoresNuevos`: Array con los valores para los campos del nuevo registro.
- **Lógica**: Verifica si los valores a insertar ya existen en la base de datos, y de ser así, emite una advertencia (`dispatch('warning')`). Si todo está correcto, se procede con la inserción y se confirma la transacción.

#### procesarCreacion($modelo, array $campos, array $valores): bool
Método auxiliar que se encarga de procesar la creación de un registro individual en el modelo.

- **Parámetros**:
  - `$modelo`: Modelo en el que se creará el nuevo registro.
  - `$campos`: Array con los nombres de los campos.
  - `$valores`: Array con los valores de los campos.
- **Devuelve**: `true` si la creación fue exitosa, `false` si hubo un conflicto de unicidad.

#### esInsercionMultiple(array $valoresNuevos): bool
Determina si los valores proporcionados corresponden a una inserción múltiple.

- **Parámetros**:
  - `$valoresNuevos`: Array con los valores que se desean insertar.
- **Devuelve**: `true` si se trata de una inserción múltiple, `false` si es una inserción individual.

#### existeValorNormalizado($modelo, string $campo, string $valorNormalizado, $idActual = null): bool
Verifica si ya existe un valor normalizado en el modelo, excluyendo el registro actual.

- **Parámetros**:
  - `$modelo`: Modelo en el que se realizará la búsqueda.
  - `$campo`: Campo en el que se realizará la verificación.
  - `$valorNormalizado`: Valor normalizado que se desea verificar.
  - `$idActual`: ID del registro actual para excluirlo de la búsqueda.
- **Devuelve**: `true` si el valor ya existe, `false` en caso contrario.

### Consideraciones de Uso
- **Validación de Datos**: Antes de realizar cualquier acción de verificación o creación, se realiza una validación utilizando el método `$this->validate()`. Asegúrate de que las reglas de validación estén definidas en la clase que usa este trait.
- **Normalización de Cadenas**: Utiliza el `StringNormalizer` para normalizar los valores antes de compararlos. Esto permite que la comparación de cadenas sea más precisa, ignorando diferencias en los acentos y caracteres especiales.
- **Transacciones**: El método `create()` utiliza transacciones para garantizar la integridad de los datos. En caso de un error durante la inserción de un registro, toda la operación será revertida.

### Ejemplos de Uso
- **Verificación y Actualización**: El trait se puede utilizar cuando se necesita verificar si los valores de ciertos campos de un modelo han cambiado, actualizarlos y asegurarse de que sean únicos en la base de datos.
- **Inserción de Registros Nuevos**: Ideal para manejar la creación de registros, tanto individuales como múltiples, en un contexto donde se requiere garantizar que los valores sean únicos y se mantenga la integridad de los datos.


