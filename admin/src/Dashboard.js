import React from 'react';
import { Card, CardHeader, CardText } from 'material-ui/Card';

export default () => (
    <Card style={{ margin: '2em' }}>
        <CardHeader title="Welcome to the administration" />
        <CardText>
			<div>AntiBot - Telegramm</div>
			<div class="poweredby"><i>Powered by Koshka!!!</i></div>
		</CardText>
    </Card>
);