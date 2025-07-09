export interface Igenric {
    /**
* metodo para almacenar un registro en la bd
* @param objeto obj almacenar
*/
    persistir(objeto: any): void;
    /**
     * Edita el objeto en la bd
     * @param objeto obj a editar
     */
    editar(objeto: any): void;
    /**
     * Lista los objetos de este tipo en la bd
     */
    listar(): void;
}
