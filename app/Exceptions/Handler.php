<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
        if ($e instanceof ApiErrorException) {
            if ($request->ajax()) {
                return response()->json([
                    'type' => 'error',
                    'message' => $e->getMessage()
                ], $e->getCode());
            } else {
                return [
                    'type' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        if ($e instanceof ApiWarningException) {
            if ($request->ajax()) {
                return response()->json([
                    'type' => 'warning',
                    'message' => $e->getMessage()
                ], $e->getCode());
            } else {
                return [
                    'type' => 'warning',
                    'message' => $e->getMessage()
                ];
            }
        }

        return parent::render($request, $e);
	}

}
