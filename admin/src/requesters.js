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
			<TextField 
				label="First Name"
				source="firstName" />
			<TextField 
				label="Last Name"
				source="lastName" />
			<TextField 
				label="User Name"
				source="userName" />
			<DateField 
				label="Requsted at"
				source="accessDate" />	
			<ReferenceField 
				label="Category" 
				source="categoryId" 
				reference="categories">
				<TextField source="title" />
			</ReferenceField>
			<ReferenceField 
				label="Post" 
				source="postId" 
				reference="posts">
				<TextField source="title" />
			</ReferenceField>
		</Datagrid>
    </List>
);