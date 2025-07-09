import { RoomType } from "./room-type";

export class Room {
    id: number;
    hotel_id: number;
    room_type: RoomType
    room_number: string;
    is_available: boolean;
    created_at: Date;
    updated_at: Date;
    constructor(
        id: number,
        hotel_id: number,
        room_type: RoomType,
        room_number: string,
        is_available: boolean,
        created_at: Date,
        updated_at: Date
    ) {
        this.id = id;
        this.hotel_id = hotel_id;
        this.room_type = room_type;
        this.room_number = room_number;
        this.is_available = is_available;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
