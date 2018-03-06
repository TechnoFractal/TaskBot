import React from 'react';
import {
	List, 
	Datagrid, 
	ReferenceField, 
	TextField,
	DateField,
	BooleanField
} from 'admin-on-rest';

export const RequesterList = (props) => (
    <List {...props}>
		<Datagrid>
			<BooleanField 
				label="Is bot?"
				source="isBot" />
			<BooleanField 
				label="Enabled?"
				source="enabled" />				
			<TextField 
				label="First Name"
				source="firstName" />
			<TextField 
				label="Last Name"
				source="lastName" />
			<TextField 
				label="User Name"
				source="userName" />
		</Datagrid>
    </List>
);