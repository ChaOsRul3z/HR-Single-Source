<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'title' => Str::headline($title),
            'document_code' => Str::slug($title),
            'department' => fake()->randomElement(['HR', 'Legal', 'Finance', 'Operations']),
            'version' => 1,
            'status' => 'active',
            'is_restricted' => false,
            'effective_date' => fake()->date(),
            'summary' => fake()->paragraph(),
            'extracted_text' => fake()->paragraphs(5, true),
            'user_id' => null,
        ];
    }

    /**
     * Mark document as archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    /**
     * Mark document as restricted (HR/Admin only).
     */
    public function restricted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_restricted' => true,
        ]);
    }

    /**
     * Assign to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
