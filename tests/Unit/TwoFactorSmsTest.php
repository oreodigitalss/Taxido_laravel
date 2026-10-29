<?php

namespace Tests\Unit;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Modules\TwoFactor\SMS\TwoFactor;
use Tests\TestCase;

class TwoFactorSmsTest extends TestCase
{
    public function test_it_sends_otp_using_the_provider_post_endpoint(): void
    {
        config(['twofactor.sms.twoFactor_key' => 'test-api-key']);
        Http::fake([
            'https://2factor.in/*' => Http::response([
                'Status' => 'Success',
                'Details' => 'test-session',
            ]),
        ]);

        $result = TwoFactor::getIntent('+919876543210', '123456');

        $this->assertSame('Success', $result->Status);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://2factor.in/API/V1/test-api-key/SMS/%2B919876543210/123456'
        );
    }

    public function test_it_reports_provider_rejections(): void
    {
        config(['twofactor.sms.twoFactor_key' => 'test-api-key']);
        Http::fake([
            'https://2factor.in/*' => Http::response([
                'Status' => 'Error',
                'Details' => 'OTP request rejected',
            ], 400),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('SMS provider rejected the OTP request.');

        TwoFactor::getIntent('+919876543210', '123456');
    }
}
