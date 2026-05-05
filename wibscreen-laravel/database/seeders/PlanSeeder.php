<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::updateOrCreate(['slug' => 'free'], [
            'name' => 'Free',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'description' => 'Perfect for personal use and getting started.',
            'features' => [
                ['text' => '1 Workspace', 'bold' => true],
                ['text' => '10 Tabs per workspace', 'bold' => true],
                ['text' => '10 Email accounts', 'bold' => true],
                ['text' => '~150 hours monthly login time', 'bold' => true],
                ['text' => 'Bot support — 10 queries/mo', 'bold' => true],
                ['text' => 'Ads displayed in workspace', 'icon' => 'fa-rectangle-ad', 'color' => '#f59e0b'],
                ['text' => 'Priority support', 'cross' => true],
            ],
            'is_popular' => false,
        ]);

        Plan::updateOrCreate(['slug' => 'pro'], [
            'name' => 'Pro',
            'price_monthly' => 199,
            'price_yearly' => 1999,
            'description' => 'For power users who need more workspace and no distractions.',
            'features' => [
                ['text' => '10 Workspaces', 'bold' => true],
                ['text' => 'Unlimited Tabs', 'bold' => true],
                ['text' => 'Unlimited Email accounts', 'bold' => true],
                ['text' => '720 hours (full month) login time', 'bold' => true],
                ['text' => 'Bot support — Unlimited', 'bold' => true],
                ['text' => 'No ads — clean workspace', 'icon' => 'fa-ban', 'color' => 'text-success'],
                ['text' => 'Priority email support', 'bold' => false],
            ],
            'is_popular' => true,
        ]);

        Plan::updateOrCreate(['slug' => 'business'], [
            'name' => 'Business',
            'price_monthly' => 899,
            'price_yearly' => 8999,
            'description' => 'Custom solutions for teams, agencies, and enterprises.',
            'features' => [
                ['text' => 'Unlimited Workspaces', 'bold' => true],
                ['text' => 'Unlimited Tabs & Collections', 'bold' => true],
                ['text' => 'Unlimited Team members', 'bold' => true],
                ['text' => 'Always-on login (no time limit)', 'bold' => true],
                ['text' => 'Dedicated account manager', 'bold' => false],
                ['text' => 'Zero ads, white-label option', 'icon' => 'fa-ban', 'color' => 'text-success'],
                ['text' => 'SLA & 24/7 priority support', 'bold' => false],
            ],
            'is_popular' => false,
        ]);
    }
}
