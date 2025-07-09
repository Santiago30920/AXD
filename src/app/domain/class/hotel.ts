import { Room } from "./room";
import { Temporate } from "./temporate";

export class Hotel {
    id: number;
    name: string;
    address: string;
    city: string;
    state: string;
    capacity: number;
    phone: string;
    email: string;
    description: string;
    rooms!: Room[];
    temporate!: Temporate[];
    created_at: Date;
    updated_at: Date;
    constructor(
        id: number,
        name: string,
        address: string,
        city: string,
        state: string,
        capacity: number,
        phone: string,
        email: string,
        description: string,
        created_at: Date,
        updated_at: Date
    ) {
        this.id = id;
        this.name = name;
        this.address = address;
        this.city = city;
        this.state = state;
        this.capacity = capacity;
        this.phone = phone;
        this.email = email;
        this.description = description;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
