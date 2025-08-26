<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Script>
 */
class ScriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scriptTypes = ['opening', 'followup', 'closing', 'objection_handling', 'appointment_setting'];
        $scriptType = fake()->randomElement($scriptTypes);
        
        $scripts = [
            'opening' => [
                'オープニング - 基本版',
                'お忙しい中失礼いたします。○○会社の○○と申します。',
            ],
            'followup' => [
                'フォローアップ - 確認版',
                'お世話になっております。先日ご連絡させていただいた○○の件でお電話いたしました。',
            ],
            'closing' => [
                'クロージング - 提案版',
                'それでは、一度お時間をいただいて詳しくご説明させていただけませんでしょうか。',
            ],
            'objection_handling' => [
                '反駁処理 - 理解版',
                'おっしゃる通りですね。ただ、こういう考え方もございまして...',
            ],
            'appointment_setting' => [
                'アポ設定 - 確認版',
                'ありがとうございます。それでは来週の火曜日はいかがでしょうか。',
            ],
        ];
        
        $scriptData = $scripts[$scriptType];
        
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $scriptData[0],
            'content' => $scriptData[1],
            'script_type' => $scriptType,
            'is_active' => fake()->boolean(80), // 80%の確率でアクティブ
            'usage_count' => fake()->numberBetween(0, 50),
        ];
    }

    /**
     * アクティブなスクリプト
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * オープニングスクリプト
     */
    public function opening()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'オープニングスクリプト',
                'content' => 'お忙しい中失礼いたします。○○会社の○○と申します。本日は貴重なお時間をいただき、ありがとうございます。',
                'script_type' => 'opening',
                'is_active' => true,
            ];
        });
    }

    /**
     * クロージングスクリプト
     */
    public function closing()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'クロージングスクリプト',
                'content' => 'それでは、一度詳しくお話をお聞かせいただける機会をいただけませんでしょうか。来週のご都合はいかがでしょうか。',
                'script_type' => 'closing',
                'is_active' => true,
            ];
        });
    }
}
