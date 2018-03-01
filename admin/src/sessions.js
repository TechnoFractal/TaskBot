import React from 'react';
import {
	List,
	Datagrid, 
	ReferenceField, 
	TextField,
	DateField,
	EditButton, 
	DisabledInput,
	DeleteButton} from 'admin-on-rest';

export const SessionList = (props) => (
    <List {...props}>
		<Datagrid>
			<TextField source="id" />
			<ReferenceField 
				label="User" 
				source="userId" 
				reference="users">
				<TextField source="login" />
			</ReferenceField>
			<DateField source="created" />	
			<TextField source="ip" />
			<DeleteButton />
		</Datagrid>
    </List>
);

const SessionTitle = ({ record }) => {
    return <span>Session {record ? `"${record.id}"` : ''}</span>;
};