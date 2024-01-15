<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class StoreController extends Controller
{
    /**
     * アカウント名の重複確認し、重複していないとき新規登録を行う
     */
    public function __invoke(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $name = $request->input('name');

            // 重複禁止のバリデーション
            if (Account::where('name', $name)->exists()) {
                throw new Error('アカウント名が重複しています');
            }
            // 新規登録
            Account::create([
                'name' => $name
            ]);

            DB::commit();

            return response()->json([
                'message' => 'アカウント新規登録'
            ]);
        } catch (Error $exception) {

            DB::rollBack();
            throw $exception;
        }
    }
}
