export class Temporate {
    id: number;
    hotel_id: number;
    season: string;
    start_date: Date;
    end_date: Date;
    price_multiplier: number;
    created_at: Date;
    updated_at: Date;

    constructor(
        id: number,
        hotel_id: number,
        season: string,
        start_date: Date,
        end_date: Date,
        price_multiplier: number,
        created_at: Date,
        updated_at: Date
    ) {
        this.id = id;
        this.hotel_id = hotel_id;
        this.season = season;
        this.start_date = start_date;
        this.end_date = end_date;
        this.price_multiplier = price_multiplier;
        this.created_at = created_at;
        this.updated_at = updated_at;
    }
}
