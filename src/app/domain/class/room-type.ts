export class RoomType {
    id: number;
    name: string;
    base_price: number;
    max_capacity: number;
    description: string;
    created_at: Date;
    updated_at: Date;

    constructor(
        id: number,
        name: string,
        base_price: number,
        max_capacity: number,
        description: string,
        created_at: Date,
        updated_at: Date
    ) {
        this.id = id;
        this.name = name;
        this.base_price = base_price;
        this.max_capacity = max_capacity;
        this.description = description;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
