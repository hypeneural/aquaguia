<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add contact, social media, booking, and address fields to parks table
     * Priority: HIGH - Required by frontend team
     */
    public function up(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            // Address fields
            $table->string('address_street', 200)->nullable()->after('longitude');
            $table->string('address_neighborhood', 100)->nullable()->after('address_street');
            $table->string('address_zip_code', 10)->nullable()->after('address_neighborhood');

            // Website
            $table->string('website', 255)->nullable()->after('address_zip_code');

            // Contact fields
            $table->string('contact_phone', 30)->nullable()->after('website');
            $table->string('contact_whatsapp', 30)->nullable()->after('contact_phone');
            $table->string('contact_whatsapp_message', 255)->nullable()->after('contact_whatsapp');
            $table->string('contact_email', 100)->nullable()->after('contact_whatsapp_message');

            // Social media links
            $table->string('social_instagram', 50)->nullable()->after('contact_email');
            $table->string('social_instagram_url', 255)->nullable()->after('social_instagram');
            $table->string('social_facebook_url', 255)->nullable()->after('social_instagram_url');
            $table->string('social_youtube_url', 255)->nullable()->after('social_facebook_url');
            $table->string('social_tiktok_url', 255)->nullable()->after('social_youtube_url');
            $table->string('social_twitter_url', 255)->nullable()->after('social_tiktok_url');

            // Booking/ticket purchase
            $table->string('booking_url', 255)->nullable()->after('social_twitter_url');
            $table->boolean('booking_is_external')->default(true)->after('booking_url');
            $table->string('booking_partner_name', 100)->nullable()->after('booking_is_external');
            $table->string('booking_affiliate_code', 50)->nullable()->after('booking_partner_name');

            // Additional pricing fields
            $table->decimal('price_senior', 10, 2)->nullable()->after('price_locker');
            $table->unsignedTinyInteger('price_child_free_under')->default(3)->after('price_senior');
            $table->unsignedTinyInteger('price_senior_age_from')->default(60)->after('price_child_free_under');
            $table->decimal('price_locker_small', 10, 2)->nullable()->after('price_senior_age_from');
            $table->decimal('price_locker_large', 10, 2)->nullable()->after('price_locker_small');
            $table->decimal('price_locker_family', 10, 2)->nullable()->after('price_locker_large');
            $table->decimal('price_vip_cabana', 10, 2)->nullable()->after('price_locker_family');
            $table->decimal('price_all_inclusive', 10, 2)->nullable()->after('price_vip_cabana');
            $table->date('price_valid_until')->nullable()->after('price_all_inclusive');

            // Rating (denormalized for performance)
            $table->decimal('rating', 2, 1)->default(0)->after('family_index');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->dropColumn([
                'address_street',
                'address_neighborhood',
                'address_zip_code',
                'website',
                'contact_phone',
                'contact_whatsapp',
                'contact_whatsapp_message',
                'contact_email',
                'social_instagram',
                'social_instagram_url',
                'social_facebook_url',
                'social_youtube_url',
                'social_tiktok_url',
                'social_twitter_url',
                'booking_url',
                'booking_is_external',
                'booking_partner_name',
                'booking_affiliate_code',
                'price_senior',
                'price_child_free_under',
                'price_senior_age_from',
                'price_locker_small',
                'price_locker_large',
                'price_locker_family',
                'price_vip_cabana',
                'price_all_inclusive',
                'price_valid_until',
                'rating',
                'review_count',
            ]);
        });
    }
};
