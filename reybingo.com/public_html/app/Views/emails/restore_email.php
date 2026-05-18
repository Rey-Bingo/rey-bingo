<!DOCTYPE html>
<html>
    <body style="background-color: #222533; padding: 20px; font-size: 14px; line-height: 1.43; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
        <div style="max-width: 600px; margin: 0px auto; background-color: #fff; box-shadow: 0px 20px 50px rgba(0,0,0,0.05);">
            <table style="width: 100%;">
                <tr>
                    <td style="background-color: #fff;">
                        <img alt="logo" src="<?= site_url('assets/img/logo.png'); ?>" style="width: 120px; padding: 5px">
                    </td>
                    <td style="padding-left: 50px; text-align: right; padding-right: 20px;">
                        <a href="<?= site_url('signin'); ?>" style="color: #261D1D; text-decoration: underline; font-size: 14px; letter-spacing: 1px;"><?= translate('signin'); ?></a>
                    </td>
                </tr>
            </table>
            <div style="padding: 40px; border-top: 1px solid rgba(0,0,0,0.05);">
                <h1 style="margin-top: 0px; font-size: 1.5rem;">
                    <?= translate('hello'); ?>, <strong><?= $user['firstname']; ?></strong>.
                </h1>
                <div style="color: #636363; font-size: 14px;">
                    <?= translate('you recently requested to reset your password for your account.'); ?> <?= translate('to reset your password, enter the following security code when prompted'); ?>:
                </div>
                <a href="javascript:void(0);" style="padding: 8px 20px; background-color: #4B72FA; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;"><?= $code; ?></a>
                <a href="<?= site_url('restore/' . $token); ?>" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; display: none;"><?= translate('restore'); ?></a>
                <div style="color: #636363; font-size: 14px; margin-top: 10px;">
                    <?= translate('if you did not request a password reset, pelase ignore this email or reply to let us know, this password reset is only valid for the next 15 minutes.'); ?><br /><br /><?= translate('thanks'); ?>,<br / > <?= translate('team'); ?> <?= systemGet('name'); ?>
                </div>
                <h4 style="margin-bottom: 10px;"><?= translate('questions?'); ?></h4>
                <div style="color: #A5A5A5; font-size: 12px;">
                    <p>
                        <?= translate('if you have any questions you can simply reply to this email or find our contact information below.'); ?> <?= translate('also contact us at'); ?> <a href="javascript:void(0);" style="text-decoration: underline; color: #4B72FA;"> <?= systemGet('email'); ?></a>
                    </p>
                </div>
            </div>
            <div style="background-color: #F5F5F5; padding: 20px 40px; text-align: center;">
                <div style="color: #A5A5A5; font-size: 12px; margin-bottom: 20px; padding: 0px 20px;">
                    <?= translate('you are receiving this email because you signed up for'); ?> <?= systemGet('name'); ?>.
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
                    <div style="color: #A5A5A5; font-size: 10px; margin-bottom: 5px;">
                        <?= systemGet('address'); ?>, <?= systemGet('city'); ?> <?= systemGet('zipcode'); ?>, <?= systemGet('state'); ?>, <?= systemGet('country'); ?>
                    </div>
                    <div style="color: #A5A5A5; font-size: 10px;">
                        &copy; <?= date("Y");?> <?= APP_NAME; ?> · <?= translate('all rights reserved'); ?>.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>